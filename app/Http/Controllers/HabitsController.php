<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;

// Constants
use App\Helpers\Constants\Habits\HistoryType;
use App\Helpers\Constants\Habits\Type;
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\Habits\Habits;
use App\Models\Habits\HabitHistory;
use App\Models\Goal\Goal;

// Requests
use App\Http\Requests\Habits\HistoryRequest;
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
        $this->middleware('membership');
    }

    public function index(Request $request)
    {
        // Get logged in user
        $user = $request->user();

        // Load user's habits
        $habits = 
            Habits::where('user_id', $user->id)
                ->with('history')
                ->orderBy('custom_order')
                ->orderBy('name')->get();

        // Check for an affirmations habit
        $type_id = Type::AFFIRMATIONS_HABIT;
        $affirmations_key = $habits->search(function($h) use ($type_id){
            return $h->type_id == $type_id;
        });

        // Check for journlaing habit
        $type_id = Type::JOURNALING_HABIT;
        $journaling_key = $habits->search(function($h) use ($type_id){
            return $h->type_id == $type_id;
        });

        // Build said affirmations habit if missing
        if($affirmations_key === false)
        {
            $this->buildAffirmationsHabit($user->id);
            return redirect()->route('habits');
        }

        // Build said journaling habit if missing
        if($journaling_key === false)
        {
            $this->buildJournalingHabit($user->id);
            return redirect()->route('habits');
        }

        // Remove affirmations habit if user doesn't the setting to display the affirmations habit
        if(!$user->getSettingValue(Setting::HABITS_SHOW_AFFIRMATIONS_HABIT))
        {
            $habits->forget($affirmations_key);
        }

        // Remove journaling habit if use doesn't have the setting to display the journaling habit
        if(!$user->getSettingValue(Setting::HABITS_SHOW_JOURNALING_HABIT))
        {
            $habits->forget($journaling_key);
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
                $required_on_label .= ' ' . dayOfWeek($day) . ',';
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
        if($habit->type_id == Type::USER_GENERATED)
        {
            $habit->name = $request->get('title');
            $habit->show_todo = $request->has('show-todo');
        }

        $habit->times_daily = $request->get('times-daily');

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

        // Calculate strength
        if(!$habit->calculateStrength())
        {
            // Log error
            Log::error('Failed to calculate strength when updating habit', [
                'habit_id' => $habit->id,
            ]);
        }

        return redirect()->route('habits.view', ['habit' => $habit->uuid]);
    }

    public function destroy(Habits $habit)
    {
        // Delete automatically created todos
        foreach($habit->todos as $todo)
        {
            if(!$todo->delete())
            {
                Log::error('Failed to delete habit to-do item.', $todo->toArray());
                return redirect()->back();
            }
        }

        // Delete any corresponding goals
        $goals = Goal::where('habit_id', $habit->id)->get();
        foreach($goals as $goal)
        {
            if(!$goal->delete())
            {
                Log::error('Failed to delete goal when deleting habit.', $goal->toArray());
                return redirect()->back();
            }
        }

        if(!$habit->delete())
        {
            Log::error('Failed to delete habit.', $habit->toArray());
            return redirect()->back();
        }

        return redirect()->route('habits');
    }

    public function history(HistoryRequest $request, Habits $habit)
    {
        // We don't update the journaling/affirmations habits history
        if($habit->type_id == Type::AFFIRMATIONS_HABIT || $habit->type_id == Type::JOURNALING_HABIT)
        {
            return redirect()->back();
        }

        // Get user
        $user = $request->user();

        // Get the current datetime based on user's timezone if available
        $timezone = $user->timezone ?? 'America/Denver'; // We should really probably use a different default... we'll wait to find out how well the timezones work
        $day = new Carbon($request->get('day'), $timezone);
        $day = $day->setTimezone('UTC')->format('Y-m-d');

        // Determine status and times
        $times = 0;
        if($request->has('status-completed'))
        {
            $type_id = HistoryType::COMPLETED;

            // Set times and trim to times daily
            $times = $request->get('times');
            if($times > $habit->times_daily)
            {
                $times = $habit->times_daily;
            }
            elseif($times < 0)
            {
                $times = 0;
            }
        }
        elseif($request->has('status-skipped'))
        {
            $type_id = HistoryType::SKIPPED;
        }
        else // status-missed
        {
            $type_id = HistoryType::MISSED;
        }

        // Set notes
        $notes = null;
        if($request->has('notes'))
        {
            $notes = $request->get('notes');
        }

        // Set keys
        $keys = [
            'habit_id' => $habit->id,
            'day' => $day,
        ];

        // Set values
        $values = [
            'type_id' => $type_id,
            'notes' => $notes,
            'times' => $times,
        ];

        // Update or create history entry
        $habit_history = HabitHistory::updateOrCreate($keys, $values);

        // Update strength
        if(!$habit->calculateStrength())
        {
            Log::error('Failed to calculate strength for habit when updating history', [
                'habit_id' => $habit->id,
            ]);
        }

        // Return either habits page or details page
        return redirect()->back();
    }

    public function soonest(Habits $habit, $strength = 100)
    {
        // Evaluate how long it would take to build strength
        $evaluation_array = $habit->evaluateStrengthCalculations(false, $strength);

        // Figure out date
        $user = \Auth::user();
        $timezone = $user->timezone ?? 'America/Denver'; // We should really probably use a different default... we'll wait to find out how well the timezones work
        $carbon = new Carbon('now', $timezone);
        $carbon->addDays($evaluation_array['actual_days']);

        // Return the date
        return $carbon->format('n/j/y');
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

    /**
     * Builds a new journaling habit based on defaults
     * 
     * @return void
     */
    private function buildJournalingHabit($user_id)
    {
        // Create a new journaling habit
        $journaling_habit = new Habits();
        
        // Set user and type ids
        $journaling_habit->type_id = Type::JOURNALING_HABIT;
        $journaling_habit->user_id = $user_id;

        // Assign properties from defaults
        foreach(config('habits.defaults')[Type::JOURNALING_HABIT] as $key => $value)
        {
            $journaling_habit->$key = $value;
        }

        // Save and log any errors
        if(!$journaling_habit->save())
        {
            Log::error('Failed to create journaling habit.', $journaling_habit->toArray());
        }
    }
}
