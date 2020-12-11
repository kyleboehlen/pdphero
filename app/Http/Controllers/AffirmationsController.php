<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

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
    
    // Redirects to create/show depending on if user has affirmations
    public function index(Request $request)
    {
        // Get affirmations
        $affirmations = $request->user()->affirmations;

        // Send to create page if user has no affirmations
        if($affirmations->count() == 0)
        {
            return redirect()->route('affirmations.create');
        }

        // Send to the show page of the first affirmation
        return redirect()->route('affirmations.show', ['affirmation' => $affirmations->first()->uuid]);
    }

    // Shows the requested affirmation
    public function show(Request $request, Affirmations $affirmation)
    {
        // Get affirmations
        $affirmations = $request->user()->affirmations;

        // Get the index of the current affirmation
        $index = $affirmations->search(function($af) use ($affirmation){
            return $af->uuid == $affirmation->uuid;
        });

        // Touch updated_at to mark it viewed
        $affirmations[$index]->touch();

        // Increment to next affirmation index
        $index++;

        // Set affirmation/page number for title
        $page = $index;

        // Set the next uuuid
        $next_uuid = null;
        if($index < $affirmations->count())
        {
            // There is another affirmation, get it's uuid
            $next_uuid = $affirmations[$index]->uuid;
        }

        // Get the next affirmation
        return view('affirmations.show')->with([
            'affirmation' => $affirmation,
            'next_uuid' => $next_uuid,
            'page' => $page,
        ]);
    }

    public function create()
    {
        return view('affirmations.create');
    }

    public function edit(Affirmations $affirmation)
    {
        return view('affirmations.edit')->with([
            'affirmation' => $affirmation,
        ]);
    }
}
