<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            return redirect()->route('home'); // And redirect to the home route if authenticated
        }

        // If the user is not authenticated, send them to the root about route
        return redirect()->route('about');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
}
