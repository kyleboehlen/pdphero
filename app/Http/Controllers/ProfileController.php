<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use Log;
use Storage;

// Requests
use App\Http\Requests\Profile\UpdateNameRequest;
use App\Http\Requests\Profile\UpdateNutshellRequest;
use App\Http\Requests\Profile\UpdatePictureRequest;

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
        return redirect()->route('profile');
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
}
