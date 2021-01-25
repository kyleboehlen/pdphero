<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Constants
use App\Helpers\Constants\Habits\Type;

// Models
use App\Models\Habits\Habits;

class HabitsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('habits.uuid');
        $this->middleware('verified');
        // To-do: Add subscription middleware
    }

    public function index(Request $request)
    {
        // Get logged in user
        $user = $request->user();

        // Load user's habits
        $habits = 
            Habits::where('user_id', $user->id)
                ->with('history')
                ->orderBy('type_id')
                ->orderBy('custom_order')
                ->orderBy('name')->get();

        // Check for an affirmations habit
        $type_id = Type::AFFIRMATIONS_HABIT;
        $key = $habits->search(function($h) use ($type_id){
            return $h->type_id == $type_id;
        });

        // Build said affirmations habit if missing
        if($key === false)
        {
            $this->buildAffirmationsHabit($user->id);
            return redirect()->route('habits');
        }

        // Return habit view
        return view('habits.index')->with([
            'habits' => $habits,
        ]);
    }

    public function view(Habits $habit)
    {
        // Return detail view
        return view('habits.details')->with([
            'habit' => $habit,
        ]);
    }

    public function colorGuide()
    {
        // Return color guide view
        return view('habits.colors');
    }
    public function destroy(Habits $habit)
    {
        if(!$habit->delete())
        {
            Log::error('Failed to delete habit', $habit->toArray());
            return redirect()->back();
        }

        return redirect()->route('habits');
    }
    /**
     * Builds a new affirmations ahbit based on defaults
     * 
     * @return void
     */
    private function buildAffirmationsHabit($user_id)
    {
        // Create a new affirmations habit
        $affirmations_habit = new Habits();
        
        // Set user and type ids
        $affirmations_habit->type_id = Type::AFFIRMATIONS_HABIT;
        $affirmations_habit->user_id = $user_id;

        // Assign properties from defaults
        foreach(config('habits.defaults')[Type::AFFIRMATIONS_HABIT] as $key => $value)
        {
            $affirmations_habit->$key = $value;
        }

        // Save and log any errors
        if(!$affirmations_habit->save())
        {
            Log::error('Failed to create affirmations habit.', $affirmations_habit->toArray());
        }
    }
}
