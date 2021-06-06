<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

// Models
use App\Models\Feature\Feature;
use App\Models\Feature\FeatureVote;

class FeatureVoteController extends Controller
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
        $this->middleware('membership.black_label');
    }

    public function index()
    {
        $features = Feature::orderBy('score', 'desc')->with('votes')->get();

        return view('feature.list')->with([
            'features' => $features,
        ]);
    }

    public function details(Feature $feature)
    {
        // Instantiate vars
        $class = 'dont-care';
        $text = 'Don\'t Care About';

        // Vote search
        $user_id = \Auth::user()->id;
        $vote = $feature->votes->firstWhere('user_id', $user_id);
        if(!is_null($vote))
        {
            if($vote->value > 0)
            {
                $class = 'want';
                $text = 'Want';
            }
            elseif($vote->value < 0)
            {
                $class = 'dont-want';
                $text = 'Don\'t Want';
            }
        }

        return view('feature.details')->with([
            'feature' => $feature,
            'class' => $class,
            'text' => $text,
        ]);
    }

    public function vote(Request $request, Feature $feature)
    {
        $user_id = \Auth::user()->id;
        $vote = FeatureVote::firstOrCreate([
            'feature_id' => $feature->id,
            'user_id' => $user_id,
        ]);

        // Default -- dont-care
        $vote->value = 0;

        // Check vote checkboxes
        if($request->has('want'))
        {
            $vote->value = 1;
        }
        elseif($request->has('dont-want'))
        {
            $vote->value = -1;
        }

        if(!$vote->save())
        {
            Log::error('Failed to save Feature Vote.', [
                'vote' => $vote->toArray(),
                'request_values' => $request->all(),
            ]);
        }
        else
        {
            if(!$feature->calculateScore())
            {
                Log::error('Failed to calculate Feature score after saving new Feature Vote.', [
                    'feature' => $feature->toArray(),
                    'vote' => $vote->toArray(),
                ]);
            }
        }

        return redirect()->route('feature.details', ['feature' => $feature->uuid]);
    }
}
