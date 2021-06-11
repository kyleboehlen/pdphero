<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TutorialsController extends Controller
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

    /**
     * Home To-Do page
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return redirect(config('tutorials.link'));
    }
}
