<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DB;
use Image;
use Log;
use Storage;

// Constants
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\User\UsersSettings;

// Notifications
use App\Notifications\SMSConfirmation;

// Requests
use App\Http\Requests\Profile\DestroyRuleRequest;
use App\Http\Requests\Profile\DestroyValueRequest;
use App\Http\Requests\Profile\UpdateNameRequest;
use App\Http\Requests\Profile\UpdateNutshellRequest;
use App\Http\Requests\Profile\UpdatePictureRequest;
use App\Http\Requests\Profile\UpdateRulesRequest;
use App\Http\Requests\Profile\UpdateSMSRequest;
use App\Http\Requests\Profile\UpdateValuesRequest;
use App\Http\Requests\Profile\VerifySMSRequest;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('first_visit.messages');
        $this->middleware('verified');
        $this->middleware('membership');
    }

    public function index()
    {
        return view('profile.index')->with([
            'setting' => Setting::class,
        ]);
    }

    // Edit views
    public function editName()
    {
        return view('profile.edit.name');
    }

    public function editValues()
    {
        return view('profile.edit.values');
    }

    public function editNutshell()
    {
        return view('profile.edit.nutshell');
    }

    public function editRules()
    {
        return view('profile.edit.rules');
    }

    public function editSettings()
    {
        return view('profile.edit.settings');
    }

    // Update functions
    public function updateName(UpdateNameRequest $request)
    {
        // Get user
        $user = $request->user();

        // Get old value for logging
        $old_name = $user->name;

        // Update name
        $new_name = $request->get('name');
        $user->name = $new_name;

        if(!$user->save())
        {
            // Log error
            Log::error("Failed update user's name from $old_name to $new_name", [
                'user->id' => $user->id,
            ]);
        }

        return redirect()->route('profile');
    }

    public function updateValues(UpdateValuesRequest $request)
    {
        // Get user
        $user = $request->user();

        // Get user's current values
        $array = $user->values ?? array();

        // Add requested value
        $value = $request->get('value');
        array_push($array, $value);

        // Set values and save user
        sort($array);
        $user->values = $array;
        if(!$user->save())
        {
            Log::error("Failed to add $value to user's values", [
                'user->id' => $user->id,
            ]);
        }

        return redirect()->route('profile.edit.values');
    }

    public function updateNutshell(UpdateNutshellRequest $request)
    {
        // Get user
        $user = $request->user();

        // Get old value for logging
        $old_nutshell = $user->nutshell;

        // Update nutshell
        $new_nutshell = $request->get('nutshell');
        $user->nutshell = $new_nutshell;

        if(!$user->save())
        {
            // Log error
            Log::error("Failed update user's nutshell.", [
                'user->id' => $user->id,
                'old_nutshell' => $old_nutshell,
                'new_nutshell' => $new_nutshell,
            ]);
        }

        return redirect()->route('profile');
    }

    public function updateRules(UpdateRulesRequest $request)
    {
        // Get user
        $user = $request->user();

        // Get user's current rules
        $array = $user->rules ?? array();

        // Add requested value
        $rule = $request->get('rule');
        array_push($array, $rule);

        // Set rules and save user
        $user->rules = $array;
        if(!$user->save())
        {
            Log::error("Failed to add $rule to user's rules", [
                'user->id' => $user->id,
            ]);
        }

        return redirect()->route('profile.edit.rules');
    }

    public function updateSettings(Request $request, $id)
    {
        // Get user
        $user = $request->user();

        // Validate and get value depending what setting type was submitted
        $setting_type = config('settings.types')[$id];
        switch($setting_type)
        {
            case 'toggle';
                $validated = true;
                $value = $request->has('value');
            break;

            case 'numeric':
                $validator = Validator::make($request->all(), [
                    'value' => 'required|numeric|min:0|max:100',
                ]);
                if(!$validator->fails())
                {
                    $validated = true;
                    $value = $request->get('value');
                }
                else
                {
                    $validated = false;
                }
            break;

            case 'options':
                $validator = Validator::make($request->all(), [
                    'value' => Rule::in(array_keys(config('settings.options')[$id])),
                ]);
                if(!$validator->fails())
                {
                    $validated = true;
                    $value = $request->get('value');
                }
                else
                {
                    $validated = false;
                }
            break;

            default:
                return redirect()->route('profile.edit.settings');
            break;
        }

        // Return back with errors if validation fails
        if(!$validated)
        {
            return redirect()->back()->withErrors($validator);
        }

        // Update or insert into users_settings
        $saved = DB::table('users_settings')->updateOrInsert([
            'user_id' => $user->id,
            'setting_id' => $id,
        ], [
            'value' => $value,
        ]);

        if(!$saved)
        {
            // Log failure
            Log::error("Failed to update setting for user.", [
                'user->id' => $user->id,
                'setting_id' => $id,
            ]);
        }

        return redirect()->to(route('profile.edit.settings') . "#anchor-$id");
    }

    public function updatePicture(UpdatePictureRequest $request)
    {
        // Get user
        $user = $request->user();

        // Crop picture
        try
        {
            // Create uuid
            $user->profile_picture = uniqid('pp-', true) . '.jpg';

            // Store image
            Storage::put("profile-pictures/$user->profile_picture", Image::make($request->file('profile-picture'))->fit(600, 600)->encode('jpg')->stream()->__toString());
            if(!$user->save())
            {
                // Log error
                Log::error("Failed to save profile_picture for $user->name", [
                    'user->id' => $user->id,
                ]);
            }
        }
        catch(\Exception $e)
        {
            // Log error
            $exception_message = $e->getMessage();
            Log::critical("Failed to crop uploaded image for $user->name, not updating user.", [
                'user->id' => $user->id,
                'exception_message' => $exception_message,
            ]);
        }

        return redirect()->back();
    }

    // Delete functions
    public function destroyValue(DestroyValueRequest $request)
    {
        // Get user
        $user = $request->user();

        // Get user's current values
        $array = $user->values ?? array();

        // Delete requested value
        $value = $request->get('value');
        if(($key = array_search($value, $array)) !== false)
        {
            unset($array[$key]);
        }

        // If we're deleting the last vale
        if(count($array) == 0)
        {
            // We want to set values to null in the db so that
            // the empty values link still shows on profile index
            $array = null;
        }

        // Set values and save user
        $user->values = $array;
        if(!$user->save())
        {
            Log::error("Failed to delete $value from user's values", [
                'user->id' => $user->id,
            ]);
        }

        return redirect()->route('profile.edit.values');
    }

    public function destroyRule(DestroyRuleRequest $request)
    {
        // Get user
        $user = $request->user();

        // Get user's current rules
        $array = $user->rules ?? array();

        // Delete requested rule
        $rule = $request->get('rule');
        if(($key = array_search($rule, $array)) !== false)
        {
            unset($array[$key]);
        }

        // If we're deleting the last rule
        if(count($array) == 0)
        {
            // We want to set values to null in the db so that
            // the empty values link still shows on profile index
            $array = null;
        }

        // Set rules and save user
        $user->rules = $array;
        if(!$user->save())
        {
            Log::error("Failed to delete $rule from user's rules", [
                'user->id' => $user->id,
            ]);
        }

        return redirect()->route('profile.edit.rules');
    }

    public function destroySettings(Request $request)
    {
        // Get user
        $user = $request->user();

        // Delete all user settings
        $users_settings = UsersSettings::where('user_id', $user->id);
        if($users_settings->get()->count() > 0 && !$users_settings->delete())
        {
            Log::error("Failed to delete user's settings", [
                'user->id' => $user->id,
            ]);
        }

        return redirect()->route('profile.edit.settings');
    }

    // SMS verification functions
    public function editSMS(Request $request)
    {
        $user = $request->user();

        return view('profile.sms.update')->with([
            'user' => $user,
        ]);
    }

    public function updateSMS(UpdateSMSRequest $request)
    {
        $user = $request->user();
        $now = Carbon::now();
        $timeout = true;
        
        if(!is_null($user->sms_code_created_at))
        {
            $minutes = $now->diffInMinutes(Carbon::parse($user->sms_code_created_at));
            if($minutes < config('sms.timeout'))
            {
                $timeout = false;
                $minutes = config('sms.timeout') - $minutes;
            }
        }

        $phone_number = $request->get('phone-number');

        if($user->sms_number != $phone_number)
        {
            if(!$timeout)
            {
                return redirect()->back()->withErrors([
                    'phone-number' => "We just updated your phone number, please wait $minutes minutes before trying again.",
                ]);
            }

            $user->sms_number = $phone_number;
            $user->sms_verified_at = null;
        }
        elseif(!is_null($user->sms_verified_at))
        {
            return redirect()->route('profile');
        }

        if($timeout)
        {
            $user->sms_verify_code = rand(100, 999) . rand(100, 999);
            $user->sms_code_created_at = $now->toDateTimeString();

            if(!$user->save())
            {
                Log::error("Failed to save user after setting sms verification code created at.", [
                    'user' => $user->toArray(),
                ]);
            }

            // Send nexmo sms confirmation alert
            $user->notify(new SMSConfirmation());
        }

        return redirect()->route('profile.sms.verify.show');
    }

    public function showVerifySMS(Request $request)
    {
        $user = $request->user();

        if(is_null($user->sms_code_created_at) || strlen($user->sms_verify_code) == 0)
        {
            dd('fuck');
            return redirect()->route('profile.sms.edit');
        }

        return view('profile.sms.verify');
    }

    public function verifySMS(VerifySMSRequest $request)
    {
        $user = $request->user();

        $now = Carbon::now();
        if(is_null($user->sms_code_created_at) || $now->diffInMinutes(Carbon::parse($user->sms_code_created_at)) > config('sms.timeout'))
        {
            return redirect()->route('profile.sms.edit')->withErrors([
                'phone-number' => "SMS verification timed out, please try again.",
            ]);
        }

        if($request->get('verify-part-one') != substr($user->sms_verify_code, 0, 3))
        {
            return redirect()->route('profile.sms.edit')->withErrors([
                'verify-part-one' => "Invalid verification code.",
            ]);
        }

        if($request->get('verify-part-two') != substr($user->sms_verify_code, 3))
        {
            return redirect()->route('profile.sms.edit')->withErrors([
                'verify-part-two' => "Invalid verification code.",
            ]);
        }

        $user->sms_verified_at = $now->toDateTimeString();

        if(!$user->save())
        {
            Log::error("Failed to save sms verified at after verifying sms code.", [
                'user' => $user->toArray(),
            ]);
        }

        return redirect()->route('profile');
    }
}
