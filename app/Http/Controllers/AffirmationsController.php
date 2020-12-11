<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;

// Models
use App\Models\Affirmations\Affirmations;
use App\Models\Affirmations\AffirmationsReadLog;

// Requests
use App\Http\Requests\Affirmations\AffirmationRequest;

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

    public function store(AffirmationRequest $request)
    {
        // Get user
        $user = $request->user();

        // Instantiate affirmation
        $affirmation = new Affirmations([
            'user_id' => $user->id,
            'value' => $request->get('affirmation'),
        ]);

        if(!$affirmation->save())
        {
            // Log Error
            Log::error('Failed to store new affirmation', $affirmation->toArray());

            // Redirect back with error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to add affirmation, please try again.'
            ]);
        }

        return redirect()->route('affirmations');
    }

    public function edit(Affirmations $affirmation)
    {
        return view('affirmations.edit')->with([
            'affirmation' => $affirmation,
        ]);
    }

    public function update(AffirmationRequest $request, Affirmations $affirmation)
    {
        // Update value
        $affirmation->value = $request->get('affirmation');

        if(!$affirmation->save())
        {
            // Log Error
            Log::error('Failed to update affirmation', $affirmation->toArray());

            // Redirect back with error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to update affirmation, please try again.'
            ]);
        }

        return redirect()->route('affirmations.show', ['affirmation' => $affirmation->uuid]);
    }

    public function checkRead(Request $request)
    {
        // Get user
        $user = $request->user();

        // We're going to check if the user read the whole list of affirmations by doing 2 things.
        // First: check if the affirmations that have been updated (touch from show())
        // is also the order they are displayed in (created_at)
        if($user->affirmations->pluck('id')->toArray() != $user->affirmationsRead->pluck('id')->toArray())
        {
            // Start the user over the easy way
            return redirect()->route('affirmations');
        }

        // Second: check if how long it went to click through the affirmations 
        // to make sure they didn't just click through it w/o reading it
        $miliseconds =
            Carbon::parse($user->affirmationsRead->first()->updated_at)->diffInRealMilliseconds($user->affirmationsRead->last()->updated_at);
        if($miliseconds < ($user->affirmationsRead->count() * config('affirmations.filter_time')))
        {
            // Start the user over, and remind them to read the affirmations
            return redirect()->route('affirmations.show', $user->affirmations->first()->uuid)->withErrors([
                'warning' => 'Remember, you do have to read your affirmations for it to work :)',
            ]);
        }

        // Log the completion of reading the affirmation list
        $read = new AffirmationsReadLog([
            'user_id' => $user->id,
        ]);

        if(!$read->save())
        {
            // Log error
            Log::error('Failed to save new AffirmationsReadLog', $read->toArray());

            // Send them back to the last affirmation
            return redirect()->route('affirmations.show', $user->affirmations->first()->uuid)->withErrors([
                'Error' => 'Oops, had a hard time recording that you finished reading your affirmation list! Please try that again.',
            ]);
        }

        // Touch the first affirmation so they're now out of order for checkRead
        $user->affirmations->first()->touch();
        
        return redirect()->route('affirmations.read.show');
    }
}
