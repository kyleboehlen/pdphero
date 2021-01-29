<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;

// Constants
use App\Helpers\Constants\Habits\Type;

// Models
use App\Models\Habits\Habits;

// Requests
use App\Http\Requests\Habits\StoreRequest;
use App\Http\Requests\Habits\UpdateRequest;
use App\Http\Requests\Habits\ViewRequest;

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

    public function view(Habits $habit, ViewRequest $request)
    {
        // Build the required on label
        $required_on_label = 'Required ';
        if($habit->times_daily > 1)
        {
            $required_on_label .= "$habit->times_daily times ";
        }
        if(!is_null($habit->days_of_week))
        {
            $required_on_label .= 'on:';
            foreach($habit->days_of_week as $day)
            {
                // Turn w day value into long day
                $required_on_label .= ' ' . jddayofweek($day, CAL_DOW_LONG) . ',';
            }
            
            // Strip last comma
            $required_on_label = substr($required_on_label, 0, strlen($required_on_label) - 1);
        }
        elseif($habit->every_x_days > 1)
        {
            $required_on_label .= "every $habit->every_x_days days";
        }
        else
        {
            $required_on_label .= "every day";
        }

        // Return detail view
        return view('habits.details')->with([
            'habit' => $habit,
            'history_offset' => $request->has('history-offset') ? $request->get('history-offset') : 0,
            'required_on_label' => $required_on_label,
        ]);
    }

    public function colorGuide()
    {
        // Return color guide view
        return view('habits.colors');
    }

    public function create()
    {
        // Return create view
        return view('habits.create');
    }

    public function store(StoreRequest $request)
    {
        // Get user
        $user = $request->user();

        // Build base attr array for creating a habit
        $attr = [
            'user_id' => $user->id,
            'type_id' => Type::USER_GENERATED,
            'name' => $request->get('title'),
            'times_daily' => $request->get('times-daily'),
            'show_todo' => $request->has('show-todo'),
        ];

        // Create habit
        $habit =  new Habits($attr);

        // Determine how the required on days is set
        if($request->has('days-of-week'))
        {
            $habit->days_of_week = $request->get('days-of-week');
        }
        else // Request has every_x_days (required_without:days_of_week)
        {
            $habit->every_x_days = $request->get('every-x-days');
        }

        // Add notes if we have em
        if($request->has('notes'))
        {
            $habit->notes = $request->get('notes');
        }

        if(!$habit->save())
        {
            // Log error
            Log::error('Failed to store new habit', $habit->toArray());

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to create your habit, please try again.'
            ]);
        }

        return redirect()->route('habits');
    }

    public function edit(Habits $habit)
    {
        // Return edit view with habit
        return view('habits.edit')->with([
            'habit' => $habit,
        ]);
    }

    public function update(UpdateRequest $request, Habits $habit)
    {
        // Update values
        $habit->name = $request->get('title');
        $habit->times_daily = $request->get('times-daily');
        $habit->show_todo = $request->has('show-todo');

        // Determine how the required on days is set
        if($request->has('days-of-week'))
        {
            $habit->days_of_week = $request->get('days-of-week');
            $habit->every_x_days = null;
        }
        else // Request has every_x_days (required_without:days_of_week)
        {
            $habit->every_x_days = $request->get('every-x-days');
            $habit->days_of_week = null;
        }

        // Add notes if we have em
        if($request->has('notes'))
        {
            $habit->notes = $request->get('notes');
        }

        if(!$habit->save())
        {
            // Log error
            Log::error('Failed to update habit', [
                'habit' => $habit->toArray(),
                'request_values' => $request->all(),
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to update your habit, please try again.'
            ]);
        }

        return redirect()->route('habits.view', ['habit' => $habit->uuid]);
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
