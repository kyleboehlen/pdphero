<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
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
use App\Http\Requests\Goal\ManualProgressRequest;
use App\Http\Requests\Goal\ShiftDatesRequest;
use App\Http\Requests\Goal\StoreRequest;
use App\Http\Requests\Goal\StoreActionItemRequest;
use App\Http\Requests\Goal\StoreCategoryRequest;
use App\Http\Requests\Goal\UpdateRequest;
use App\Http\Requests\Goal\UpdateActionItemRequest;

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

    public function toggleAchievedGoal(Request $request, Goal $goal)
    {
        // Toggle goal achieved and save
        $goal->achieved = !$goal->achieved;

        if(!$goal->save())
        {
            Log::error('Failed to toggle achieved on goal', $goal->toArray());
        }

        return redirect()->route('goals.view.goal', [
            'goal' => $goal->uuid,
        ]);
    }

    public function toggleAchievedActionItem(Request $request, GoalActionItem $action_item)
    {
        // Toggle action item achieved and save
        $action_item->achieved = !$action_item->achieved;

        if(!$action_item->save())
        {
            Log::error('Failed to toggle achieved on goal action item', $action_item->toArray());
        }

        // Redirect back to action item details or not by checking show_details
        if($request->has('view_details') && $request->get('view_details'))
        {
            return redirect()->route('goals.view.action-item', [
                'action_item' => $action_item->uuid,
            ]);
        }

        return redirect()->route('goals.view.goal', [
            'goal' => $action_item->goal->uuid,
        ]);
    }

    public function viewGoal(Request $request, Goal $goal)
    {
        // Build nav and tab dropdowns based on goal type
        $nav_show = 'back|edit|delete';

        if($goal->type_id == Type::PARENT_GOAL)
        {
            $nav_show .= '|create-sub';
        }

        if($goal->type_id == Type::ACTION_DETAILED || $goal->type_id == Type::ACTION_AD_HOC)
        {
            $nav_show .= '|create-action-item';
        }

        if($goal->type_id == Type::ACTION_DETAILED || $goal->type_id == Type::PARENT_GOAL)
        {
            $nav_show .= '|shift-dates';
        }

        if($goal->type_id == Type::FUTURE_GOAL)
        {
            $nav_show .= '|convert-active';
        }
        else
        {
            if($goal->achieved)
            {
                $nav_show .= '|toggle-unachieved';
            }
            else
            {
                $nav_show .= '|toggle-achieved';
            }
        }

        if($goal->type_id == Type::MANUAL_GOAL)
        {
            $nav_show .= '|update-manual-progress';
        }

        if(!is_null($goal->parent_id))
        {
            $nav_show = str_replace('back', 'parent-back', $nav_show);
        }

        // Build dropdown nav
        $dropdown_nav = [
            'details' => 'Details',
        ];

        if($goal->type_id != Type::FUTURE_GOAL)
        {
            $dropdown_nav['progress'] = 'Progress';
        }

        if($goal->type_id == Type::PARENT_GOAL)
        {
            $dropdown_nav['sub-goals'] = 'Sub Goals';
        }

        if($goal->type_id == Type::ACTION_DETAILED || $goal->type_id == Type::ACTION_AD_HOC)
        {
            $dropdown_nav['action-plan'] = 'Action Plan';
        }

        if($goal->type_id == Type::ACTION_AD_HOC)
        {
            $dropdown_nav['ad-hoc-list'] = 'Ad Hoc List';
        }

        $dropdown_nav['show-all'] = 'Show All';

        // Load extra info needed for view
        $goal->load('category', 'status');

        if($goal->type_id == Type::PARENT_GOAL)
        {
            $goal->load('subgoals');
        }

        if(!is_null($goal->parent_id))
        {
            $goal->load('parent');
        }

        // Return details view
        return view('goals.details')->with([
            'goal' => $goal,
            'nav_show' => $nav_show,
            'dropdown_nav' => $dropdown_nav,
            'status' => Status::class,
            'type' => Type::class,
        ]);
    }

    public function viewActionItem(Request $request, GoalActionItem $action_item)
    {
        // Build nav
        $show = 'back-goal|edit|delete';
        if($action_item->achieved)
        {
            $show .= '|toggle-unachieved';
        }
        else
        {
            $show .= '|toggle-achieved';
        }

        // Load goal for nav and forms
        $action_item->load('goal');

        // Return detail view
        return view('goals.action-item-details')->with([
            'action_item' => $action_item,
            'show' => $show,
        ]);
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

    public function createActionItem(Request $request, Goal $goal)
    {
        return view('goals.create-action-item')->with([
            'goal' => $goal,
        ]);
    }

    public function storeCategory(StoreCategoryRequest $request)
    {
        // Create category
        $category = new GoalCategory([
            'name' => $request->name,
            'user_id' => $request->user()->id,
        ]);

        // Save/log errors
        if(!$category->save())
        {
            Log::error('Failed to save goal category', $category->toArray());
            return redirect()->back();
        }

        return redirect()->route('goals.edit.categories');
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
        if($type_id == Type::ACTION_AD_HOC && $request->get('custom-times') && $request->get('time-period'))
        {
            $goal->custom_times = $request->get('custom-times');
            $goal->time_period_id = $request->get('time-period');
        }

        // Manual options
        if($type_id == Type::MANUAL_GOAL && $request->has('custom-times') && $request->get('time-period'))
        {
            $goal->custom_times = $request->get('custom-times');
            $goal->time_period_id = $request->get('time-period');
            $goal->manual_completed = 0;
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
            $parent_goal = Goal::where('uuid', $request->get('parent-goal'))->first();
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

        // Get future goal if exsists
        $future_goal = null;
        if($request->has('future-goal'))
        {
            $future_goal = Goal::where('uuid', $request->get('future-goal'))->first();
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
        elseif(!is_null($future_goal))
        {
            // Copy image
            try
            {
                Storage::copy('/public/goal-images/' . $future_goal->uuid . '.png', '/public/goal-images/' . $goal->uuid . '.png');
                $goal->use_custom_img = true;
                if(!$goal->save())
                {
                    Log::error("Failed to set user_custom_image to true after copying future goal image.", [
                        'goal->id' => $goal->id,
                        'future_goal->id' => $future_goal->id,
                    ]);
                }
            }
            catch(\Exception $e)
            {
                // Log error
                $exception_message = $e->getMessage();
                Log::error("Failed to copy future goal image", [
                    'goal->id' => $goal->id,
                    'future_goal->id' => $future_goal->id,
                    'exception_message' => $exception_message,
                ]);
            }
        }

        // Here we delete a future goal if we successfully turned it into a new goal
        if($type_id != Type::FUTURE_GOAL && !is_null($future_goal))
        {
            // Delete future goal
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

    public function storeActionItem(StoreActionItemRequest $request, Goal $goal)
    {
        // Get base attributes
        $attr = [
            'goal_id' => $goal->id,
            'name' => $request->get('name'),
            'notes' => $request->get('notes'),
        ];

        // Create the action item
        $action_item = new GoalActionItem($attr);

        // If it's a detailed action item goal, set deadline
        if($goal->type_id == Type::ACTION_DETAILED)
        {
            $action_item->deadline = $request->get('deadline');
        }

        // Set show todo settings if overriden
        if($request->has('override-show-todo'))
        {
            if($request->has('show-todo'))
            {
                $action_item->override_show_todo = true;

                // Set days
                $action_item->override_todo_days_before = $request->get('show-todo-days-before');
            }
            else
            {
                $action_item->override_show_todo = false;
            }
        }

        // Save action item
        if(!$action_item->save())
        {
            // Log error
            Log::error('Failed to store goal action item.', [
                'user->id' => $user->id,
                'action_item' => $action_item->toArray(),
                'request_values' => $request->all(),
            ]);
        }

        return redirect()->route('goals.view.action-item', ['action_item' => $action_item->uuid]);
    }

    public function editCategories(Request $request)
    {
        // Get users categories
        $categories = $request->user()->goalCategories;

        // Return edit view
        return view('goals.categories')->with([
            'categories' => $categories,
        ]);
    }

    public function editGoal(Request $request, Goal $goal)
    {
        return view('goals.edit')->with(['goal' => $goal]);
    }

    public function editActionItem(Request $request, GoalActionItem $action_item)
    {
        // Load goal for nav and form
        $action_item->load('goal');

        return view('goals.edit-action-item')->with([
            'action_item' => $action_item,
        ]);
    }

    public function shiftDates(ShiftDatesRequest $request, Goal $goal)
    {
        $days = $request->get('shift-days');

        if($days < 0)
        {
            $days = abs($days);
            $goal->shiftDates($days, 'sub');
        }
        elseif($days > 0)
        {
            $goal->shiftDates($days);
        }

        return redirect()->route('goals.view.goal', ['goal' => $goal->uuid]);
    }

    public function updateGoal(UpdateRequest $request, Goal $goal)
    {
        // Set user
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

        // Start setting goal values
        $goal->name = $name;
        $goal->reason = $request->get('reason');

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
        if($request->has('notes') && $goal->type_id != Type::HABIT_BASED) // Habit goals get notes from the habit
        {
            $goal->notes = $request->get('notes');
        }
        elseif($goal->type_id == Type::HABIT_BASED && !is_null($habit->notes))
        {
            $goal->notes = $habit->notes;
        }

        // Ad hoc/manual goal options
        if(($goal->type_id == Type::ACTION_AD_HOC || $goal->type_id == Type::MANUAL_GOAL) && $request->get('custom-times') && $request->get('time-period'))
        {
            $goal->custom_times = $request->get('custom-times');
            $goal->time_period_id = $request->get('time-period');
        }

        // Habit options
        if($goal->type_id == Type::HABIT_BASED && !is_null($habit_strength) && !is_null($habit))
        {
            $goal->habit_strength = $habit_strength;
            $goal->habit_id = $habit->id;
        }
        
        // Dates and shit
        if($goal->type_id != Type::FUTURE_GOAL) // Future goals don't have dates
        {
            // All goals have an end-date
            if($request->has('end-date'))
            {
                $goal->end_date = $request->get('end-date'); // Are we gonna need to do some sort of timezone conversion here?
            }

            // Habit goals don't have a start-date
            if($goal->type_id != Type::HABIT_BASED && $request->has('start-date'))
            {
                $goal->start_date = $request->get('start-date'); // Are we gonna need to do some sort of timezone conversion here?
            }
        }

        // Default show-todo settings
        if(in_array($goal->type_id, [Type::ACTION_AD_HOC, Type::ACTION_DETAILED, Type::PARENT_GOAL]))
        {
            if($request->has('show-todo'))
            {
                $goal->default_show_todo = true;
                $goal->default_todo_days_before = $request->get('show-todo-days-before');
            }
        }

        // Check for parent goal
        if($goal->type_id != Type::FUTURE_GOAL && $request->has('parent-goal'))
        {
            // Get parent goal id
            $parent_goal = Goal::where('uuid', $request->get('parent-goal'))->first();
            $goal->parent_id = $parent_goal->id;
        }

        if($goal->save())
        {
            // Log error
            Log::error('Failed to update goal.', [
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
                    Log::error("Failed to set user_custom_image to true after updating goal image.", [
                        'goal->id' => $goal->id,
                    ]);
                }
            }
            catch(\Exception $e)
            {
                // Log error
                $exception_message = $e->getMessage();
                Log::error("Failed to crop, encode, and save updated goal image.", [
                    'goal->id' => $goal->id,
                    'exception_message' => $exception_message,
                ]);
            }
        }
        
        // Return goal detail view
        return redirect()->route('goals.view.goal', ['goal' => $goal->uuid]);
    }

    public function updateActionItem(UpdateActionItemRequest $request, GoalActionItem $action_item)
    {
        if($request->has('name'))
        {
            $action_item->name = $request->get('name');
        }

        if($request->has('notes'))
        {
            $action_item->notes = $request->get('notes');
        }

        if($request->has('deadline'))
        {
            $action_item->deadline = $request->get('deadline');
        }

        // Set show todo settings if overriden
        if($request->has('override-show-todo'))
        {
            if($request->has('show-todo'))
            {
                $action_item->override_show_todo = true;

                // Set days
                $action_item->override_todo_days_before = $request->get('show-todo-days-before');
            }
            else
            {
                $action_item->override_show_todo = false;
            }
        }
        elseif($request->has('override-options'))
        {
            $action_item->override_show_todo = null;
            $action_item->override_todo_days_before = 0;
        }

        // Save action item
        if(!$action_item->save())
        {
            // Log error
            Log::error('Failed to update goal action item.', [
                'user->id' => $user->id,
                'action_item' => $action_item->toArray(),
                'request_values' => $request->all(),
            ]);
        }

        return redirect()->route('goals.view.action-item', ['action_item' => $action_item->uuid]);
    }

    public function updateManualProgress(ManualProgressRequest $request, Goal $goal)
    {
        // This route is only for manual goals
        if($goal->type_id == Type::MANUAL_GOAL)
        {
            $manual_completed = $request->get('manual-completed');
            if($manual_completed > ($goal->custom_times + config('goals.manual_goal_buffer')))
            {
                $manual_completed = $goal->custom_times;
            }
            elseif($manual_completed < 0)
            {
                $manual_completed = 0;
            }
            
            $goal->manual_completed = $manual_completed;
            
            if(!$goal->save())
            {
                // Log error
                Log::error('Failed to update manual completed for goal', $goal->toArray());
            }
            else
            {
                if(!$goal->calculateProgress())
                {
                    // Log error
                    Log::error('Failed to calculate progress for goal after updating manual completed', $goal->toArray());
                }
            }
        }

        // Return goal detail view
        return redirect()->route('goals.view.goal', ['goal' => $goal->uuid]);
    }

    public function destroyCategory(Request $request, GoalCategory $category)
    {
        // Remove category from goals with that category
        Goal::where('user_id', $request->user()->id)->where('category_id', $category->id)->update(['category_id' => null]);

        // Delete category
        if(!$category->delete())
        {
            Log::error('Failed to delete category', $category->toArray());
            return redirect()->back();
        }

        return redirect()->route('goals.edit.categories');
    }

    public function destroyGoal(Goal $goal)
    {
        // Delete goal
        if(!$goal->delete())
        {
            Log::error('Failed to delete goal', $goal->toArray());
            return redirect()->back();
        }

        return redirect()->route('goals');
    }

    public function destroyActionItem(GoalActionItem $action_item)
    {
        // Delete action item
        if(!$action_item->delete())
        {
            Log::error('Failed to delete goal action item', $action_item->toArray());
            return redirect()->back();
        }

        // Load goal for redirect
        $action_item->load('goal');

        return redirect()->route('goals.view.goal', ['goal' => $action_item->goal->uuid]);
    }
}
