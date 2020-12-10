<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\Affirmations\Affirmations;

class AffirmationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('affirmations.uuid');
        $this->middleware('verified');
    }
    
}
