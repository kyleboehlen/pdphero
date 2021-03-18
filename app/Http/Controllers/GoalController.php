<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use Log;

// Constants
use App\Helpers\Constants\Goal\Status;
use App\Helpers\Constants\Goal\Type;

// Models
use App\Models\Goal\Goal;
use App\Models\Goal\GoalActionItem;
use App\Models\Goal\GoalCategory;
use App\Models\Goal\GoalType;
use App\Models\Habits\Habits;
use App\Models\Relationships\GoalsHabits;

// Requests
use App\Http\Requests\Goal\CreateRequest;
use App\Http\Requests\Goal\StoreRequest;

class GoalController extends Controller
{
    private $scopes = [
        'all', 'achieved', 'active', 'future',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('goal.uuid');
        $this->middleware('goal.action_item.uuid');
        $this->middleware('goal.category.uuid');
        $this->middleware('verified');
        // To-do: Add subscription middleware
    }

    public function index($scope = 'active', GoalCategory $category = null)
    {
        // Quick dirty scope validation
        if(!in_array($scope, $this->scopes))
        {
            // Return to index with default values
            return redirect()->route('goals');
        }

        // Get user
        $user = \Auth::user();

        // Build goals query
        $goals = Goal::where('user_id', $user->id)->whereNull('parent_id');

        // Filter by scope
        switch($scope)
        {
            case 'achieved':
                $goals = $goals->where('achieved', true)->where('type_id', '!=', Type::FUTURE_GOAL);
                break;
            
            case 'active':
                $goals = $goals->where('achieved', false)->where('type_id', '!=', Type::FUTURE_GOAL);
                break;

            case 'future':
                $goals = $goals->where('type_id', Type::FUTURE_GOAL);
                break;
        }

        // Filter by category
        if(!is_null($category))
        {
            $goals = $goals->where('category_id', $category->id);
        }

        // Load functions
        $goals = $goals->with('status')->get();
        $categories = $user->goalCategories;

        return view('goals.index')->with([
            'goals' => $goals,
            'scopes' => $this->scopes,
            'selected_scope' => $scope,
            'categories' => $categories,
            'selected_category' => $category,
        ]);
    }

    public function types()
    {
        return view('goals.types');
    }

    public function toggleCompletedGoal(Request $request, Goal $goal)
    {

    }

    public function toggleCompletedActionItem(Request $request, GoalActionItem $action_item)
    {

    }

    public function viewGoal(Request $request, Goal $goal)
    {

    }

    public function viewActionItem(Request $request, GoalActionItem $action_item)
    {

    }

    public function createGoal(CreateRequest $request)
    {
        // Set optional vars from request
        $future_goal_uuid = null;
        $parent_goal_uuid = null;
        $type_id = null;

        if($request->has('future-goal'))
        {
            $future_goal_uuid = $request->get('future-goal');
        }

        if($request->has('parent-goal'))
        {
            $parent_goal_uuid = $request->get('parent-goal');
        }

        if($request->has('type'))
        {
            $type_id = $request->get('type');
        }


        // If we don't know what type we're creating
        if(is_null($type_id))
        {
            // Return the type selector view
            return view('goals.create-selector')->with([
                'goal_types' => GoalType::all(),
                'future_goal_uuid' => $future_goal_uuid,
                'parent_goal_uuid' => $parent_goal_uuid,
                'type' => Type::class,
            ]);
        }

        // Otherwise, let's create that goal yoooo!!
        return view('goals.create')->with([
            'type_id' => $type_id,
            'future_goal_uuid' => $future_goal_uuid,
            'parent_goal_uuid' => $parent_goal_uuid,
        ]);
    }

    public function createActionItem(Request $request, GoalActionItem $action_item)
    {

    }

    public function storeGoal(StoreRequest $request)
    {
        // Set type and user
        $type_id = $request->get('type');
        $user = $request->user();

        if($request->has('name'))
        {
            $name = $request->get('name');
        }
        else // Has habit and habit strength
        {
            $habit = Habits::where('uuid', $request->get('habit'))->first();
            $habit_strength = $request->get('habit-strength');

            // Build name
            $name = "$habit_strength% Strength on $habit->name Habit";
        }

        $attr = [
            'name' => $name,
            'reason' => $request->get('reason'),
            'type_id' => $type_id,
            'user_id' => $user->id,
            'status_id' => Status::TBD,
        ];

        $goal = new Goal($attr);

        // Check category
        if($request->has('category'))
        {
            $category_uuid = $request->get('category');
            if($category_uuid !== 'no-category')
            {
                $category = GoalCategory::where('uuid', $category_uuid)->first();
                $goal->category_id = $category->id;
            }
        }

        // Check notes
        if($request->has('notes') && $type_id != Type::HABIT_BASED) // Habit goals get notes from the habit
        {
            $goal->notes = $request->get('notes');
        }
        elseif($type_id == Type::HABIT_BASED && !is_null($habit->notes))
        {
            $goal->notes = $habit->notes;
        }

        // Ad hoc options
        if($type_id == Type::ACTION_AD_HOC && $request->get('ad-hoc-number') && $request->get('ad-hoc-period'))
        {
            $goal->custom_times = $request->get('ad-hoc-number');
            $goal->ad_hoc_period_id = $request->get('ad-hoc-period');
        }

        // Manual options
        if($type_id == Type::MANUAL_GOAL && $request->has('manual-number'))
        {
            $goal->custom_times = $request->get('manual-number');
        }

        // Habit options
        if($type_id == Type::HABIT_BASED && !is_null($habit_strength) && !is_null($habit))
        {
            $goal->habit_strength = $habit_strength;
            $goal->habit_id = $habit->id;
        }
        
        // Dates and shit
        if($type_id != Type::FUTURE_GOAL) // Future goals don't have dates
        {
            // All goals have an end-date
            if($request->has('end-date'))
            {
                $goal->end_date = $request->get('end-date'); // Are we gonna need to do some sort of timezone conversion here?
            }

            // Habit goals don't have a start-date
            if($type_id != Type::HABIT_BASED && $request->has('start-date'))
            {
                $goal->start_date = $request->get('start-date'); // Are we gonna need to do some sort of timezone conversion here?
            }
        }

        // Default show-todo settings
        if(in_array($type_id, [Type::ACTION_AD_HOC, Type::ACTION_DETAILED, Type::PARENT_GOAL]))
        {
            if($request->has('show-todo'))
            {
                $goal->default_show_todo = true;
                $goal->default_todo_days_before = $request->get('show-todo-days-before');
            }
        }

        // Check for parent goal
        if($type_id != Type::FUTURE_GOAL && $request->has('parent-goal'))
        {
            // Get parent goal id
            $parent_goal = Goal::where('uuid', $request->get('future-goal'))->first();
            $goal->parent_id = $parent_goal->id;
        }

        if($goal->save())
        {
            // Log error
            Log::error('Failed to store new goal.', [
                'user->id' => $user->id,
                'goal' => $goal->toArray(),
                'request_values' => $request->all(),
            ]);
        }

        // Save custom image
        if($request->has('goal-image'))
        {
            // Crop/save/encode image
            try
            {
                Image::make($request->file('goal-image'))->fit(600, 600)->encode('png')->save(storage_path() . '/app/public/goal-images/' . $goal->uuid . '.png');
                $goal->use_custom_img = true;
                if(!$goal->save())
                {
                    Log::error("Failed to set user_custom_image to true after saving goal image.", [
                        'goal->id' => $goal->id,
                    ]);
                }
            }
            catch(\Exception $e)
            {
                // Log error
                $exception_message = $e->getMessage();
                Log::error("Failed to crop, encode, and save goal image.", [
                    'goal->id' => $goal->id,
                    'exception_message' => $exception_message,
                ]);
            }
        }

        // Here we delete a future goal if we successfully turned it into a new goal
        if($type_id != Type::FUTURE_GOAL && $request->has('future-goal'))
        {
            // Delete future goal
            $future_goal = Goal::where('uuid', $request->get('future-goal'))->first();
            if(!$future_goal->delete())
            {
                Log::error("Failed to delete future goal when storing goal.", [
                    'goal->id' => $goal->id,
                    'future_goal->id' => $future_goal->id,
                ]);
            }
        }
        
        // Return goal detail view
        return redirect()->route('goals.view.goal', ['goal' => $goal->uuid]);
    }

    public function storeActionItem(Request $request, GoalActionItem $action_item)
    {

    }

    public function editGoal(Request $request, Goal $goal)
    {

    }

    public function editActionItem(Request $request, GoalActionItem $action_item)
    {

    }

    public function updateGoal(Request $request, Goal $goal)
    {

    }

    public function updateActionItem(Request $request, GoalActionItem $action_item)
    {

    }

    public function destroyGoal(Request $request, Goal $goal)
    {
        // Delete goal
        if(!$goal->delete())
        {
            Log::error('Failed to delete goal', $goal->toArray());
            return redirect()->back();
        }

        return redirect()->route('goals');
    }

    public function destroyActionItem(Request $request, GoalActionItem $action_item)
    {
        // Delete action item
        if(!$action_item->delete())
        {
            Log::error('Failed to delete goal action item', $action_item->toArray());
            return redirect()->back();
        }

        return redirect()->route('goals');
    }
}
