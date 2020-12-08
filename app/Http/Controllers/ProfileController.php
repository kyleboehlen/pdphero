<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Image;
use Log;
use Storage;

// Constants
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\User\UsersSettings;

// Requests
use App\Http\Requests\Profile\DestroyValueRequest;
use App\Http\Requests\Profile\UpdateNameRequest;
use App\Http\Requests\Profile\UpdateNutshellRequest;
use App\Http\Requests\Profile\UpdatePictureRequest;
use App\Http\Requests\Profile\UpdateValuesRequest;

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
        $this->middleware('verified');
    }

    public function index()
    {
        return view('profile.index');
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

    public function updateSettings(Request $request, $id)
    {
        // Get user
        $user = $request->user();

        // Validate and get value depending on which setting
        switch($id)
        {
            case Setting::TODO_MOVE_COMPLETED:
                $validated = true;
                $value = $request->has('show');
            break;

            case Setting::TODO_SHOW_COMPLETED_FOR:
                $validator = Validator::make($request->all(), [
                    'hours' => 'required|numeric|min:0|max:100',
                ]);
                if(!$validator->fails())
                {
                    $validated = true;
                    $value = $request->get('hours');
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

        return redirect()->route('profile.edit.settings', ["#$id"]);
    }

    public function updatePicture(UpdatePictureRequest $request)
    {
        // Get user
        $user = $request->user();
        
        // Save image
        $user->profile_picture = 
            str_replace(
                'public/profile-pictures/', '',
                $request->file('profile-picture')->store('public/profile-pictures')
            );

        // Crop picture
        try
        {
            Image::make(storage_path() . '/app/public/profile-pictures/' . $user->profile_picture)->fit(600, 600)->save();
        }
        catch(\Exception $e)
        {
            // Log error
            Log::critical("Failed to crop uploaded image for $user->name, attempting to set user->profile_picture back to null", [
                'user->id' => $user->id,
            ]);

            // Reset profile picture attribute
            $user->profile_picture = null;

            // Save user
            if(!$user->save())
            {
                // Log error
                Log::alert("Failed to null out profile_picture for $user->name after crop failed", [
                    'user->id' => $user->id,
                ]);
            }
        }

        if(!$user->save())
        {
            // Log error
            Log::error("Failed to save profile_picture for $user->name", [
                'user->id' => $user->id,
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

    public function destroySettings(Request $request)
    {
        // Get user
        $user = $request->user();

        // Delete all user settings
        if(!UsersSettings::where('user_id', $user->id)->delete())
        {
            Log::error("Failed to delete user's settings", [
                'user->id' => $user->id,
            ]);
        }

        return redirect()->route('profile.edit.settings');
    }
}
