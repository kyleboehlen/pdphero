<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

// Models
use App\Models\Home\Home;
use App\Models\Relationships\UsersHideHome;

class HomeController extends Controller
{
    /**
     * Handles redirecting to the proper about splash page if user is unauthenticated
     * or redirecting to the home route if user is authenticated
     *
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function index()
    {
        if(\Auth::check()) // Check if the user is authenticated
        {
            return redirect()->route('todo.list'); // And redirect to the to-do page if authenticated
        }

        // If the user is not authenticated, send them to the root about route
        return redirect()->route('about');
    }

    /**
     * Sets the refer slug to the session and redirects back to the index
     *
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function refer($slug)
    {
        // Set the refer slug to the session
        session(['refer_slug' => strtoupper($slug)]);

        // Redirect to the root index
        return redirect()->route('root');
    }

    /**
     * Sends the user to the default landing to-do page if redirected from root
     * or returns the home view if directed from anywhere else
     *
     * @return \Illuminate\Support\Facades\Redirect
     * @return Response
     */
    public function home(Request $request)
    {
        // I've changed the RouteServiceProvider::HOME route to /todo
        return view('home.index'); // This returns a home page with icons for tools, tutorials, etc
    }

    public function edit(Request $request)
    {
        return view('home.edit');
    }

    public function hide(Request $request, Home $home)
    {
        $user = $request->user();

        if(!in_array($home->id, $user->hideHomeArray()))
        {
            $hide_home = new UsersHideHome([
                'user_id' => $user->id,
                'home_id' => $home->id,
            ]);

            if(!$hide_home->save())
            {
                Log::error('Failed to hide home', [
                    'user_id' => $user->id,
                    'home_id' => $home->id,
                ]);
            }
        }

        return redirect()->route('home.edit');
    }

    public function show(Request $request, Home $home)
    {
        $user = $request->user();

        if(in_array($home->id, $user->hideHomeArray()))
        {
            if(!UsersHideHome::where('user_id', $user->id)->where('home_id', $home->id)->delete())
            {
                Log::error('Failed to show  home', [
                    'user_id' => $user->id,
                    'home_id' => $home->id,
                ]);
            }
        }

        return redirect()->route('home.edit');
    }
}
