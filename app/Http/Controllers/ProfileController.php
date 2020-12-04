<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

// Requests
use App\Http\Requests\Profile\UpdateNameRequest;
use App\Http\Requests\Profile\UpdateNutshellRequest;

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
}
