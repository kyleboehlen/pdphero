<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models and shit

class AddictionController extends Controller
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
        $this->middleware('addiction.uuid');
        $this->middleware('addiction.milestone.uuid');
        $this->middleware('addiction.relapse.uuid');
        $this->middleware('verified');
        $this->middleware('membership');
    }

    /**
     * Addictions list view
     *
     * @return Response
     */
    public function index(Request $request)
    {

    }
}
