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
     * Sends the user to the default landing to-do page if redirected from root
     * or returns the home view if directed from anywhere else
     *
     * @return \Illuminate\Support\Facades\Redirect
     * @return Response
     */
    public function home(Request $request)
    {
        // Later on this may get changed to check what URL the request was sent from
        // and show a home view if they requested it, otherwise send to to-do
        return redirect()->route('todo.list');
    }
}
