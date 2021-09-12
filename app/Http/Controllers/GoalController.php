<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Storage;
use Image;
use Log;

// Constants
use App\Helpers\Constants\User\Setting;
use App\Helpers\Constants\Goal\Status;
use App\Helpers\Constants\Goal\Type;
use App\Helpers\Constants\Goal\TimePeriod;

// Models
use App\Models\Bucketlist\BucketlistItem;
use App\Models\Goal\Goal;
use App\Models\Goal\GoalActionItem;
use App\Models\Goal\GoalActionItemReminder;
use App\Models\Goal\GoalCategory;
use App\Models\Goal\GoalType;
use App\Models\Habits\Habits;
use App\Models\Relationships\GoalsHabits;
use App\Models\ToDo\ToDo;

// Requests
use App\Http\Requests\Goal\ConvertSubRequest;
use App\Http\Requests\Goal\CreateRequest;
use App\Http\Requests\Goal\ManualProgressRequest;
use App\Http\Requests\Goal\ShiftDatesRequest;
use App\Http\Requests\Goal\SetDeadlineRequest;
use App\Http\Requests\Goal\StoreRequest;
use App\Http\Requests\Goal\StoreActionItemRequest;
use App\Http\Requests\Goal\StoreCategoryRequest;
use App\Http\Requests\Goal\StoreReminderRequest;
use App\Http\Requests\Goal\TransferAdHocRequest;
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
        $this->middleware('bucketlist.uuid');
        $this->middleware('goal.uuid');
        $this->middleware('goal.action_item.uuid');
        $this->middleware('goal.category.uuid');
        $this->middleware('goal.reminder.uuid');
        $this->middleware('verified');
        $this->middleware('membership');
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

        // Verify status and refresh
        $this->recursivelyUpdateGoalsStatus($goals, true);

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
        else
        {
            // Toggle complete on todo for action item if exsists
            $action_item->load('todo');
            if(!is_null($action_item->todo))
            {
                $action_item->todo->completed = $action_item->achieved;
                if(!$action_item->todo->save())
                {
                    Log::error('Failed to toggle todo item when toggling achieved action item', $action_item->toArray());
                }
            }

            // Recalculate progress for the goal
            if(!$action_item->goal->calculateProgress())
            {
                Log::error('Failed to calculate progress when toggling achieved action item', $action_item->toArray());
            }
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
            'selected-dropdown' => 'action-plan',
        ]);
    }

    public function toggleAchievedBucketlistItem(Request $request, BucketlistItem $bucketlist_item)
    {
        // Toggle bucketlist item achieved and save
        $bucketlist_item->achieved = !$bucketlist_item->achieved;

        if(!$bucketlist_item->save())
        {
            Log::error('Failed to toggle achieved on goal bucketlist item', $bucketlist_item->toArray());
        }
        else
        {
            // Recalculate progress for the goal
            if(!$bucketlist_item->goal->calculateProgress())
            {
                Log::error('Failed to calculate progress when toggling achieved bucketlist item', $bucketlist_item->toArray());
            }
        }

        // Redirect back to action item details or not by checking show_details
        if($request->has('view_details') && $request->get('view_details'))
        {
            return redirect()->route('goals.view.bucketlist-item', [
                'bucketlist_item' => $bucketlist_item->uuid,
            ]);
        }

        return redirect()->route('goals.view.goal', [
            'goal' => $bucketlist_item->goal->uuid,
            'selected-dropdown' => 'action-plan',
        ]);
    }

    public function viewGoal(Request $request, Goal $goal)
    {
        // Get user
        $user = $request->user();

        // Verify proper status
        if(!$goal->determineStatus())
        {
            Log::error('Failed to determine status for goal when viewing goal details', $goal->toArray());
        }
        else
        {
            $goal->refresh();
        }

        // Build nav and tab dropdowns based on goal type
        $nav_show = 'back|delete';

        if($goal->type_id == Type::PARENT_GOAL)
        {
            $nav_show .= '|create-sub';
        }

        if($goal->type_id == Type::ACTION_DETAILED || $goal->type_id == Type::ACTION_AD_HOC)
        {
            $nav_show .= '|create-action-item';
        }

        if($goal->type_id == Type::ACTION_AD_HOC)
        {
            $nav_show .= '|transfer-ad-hoc-items';
        }

        if($goal->type_id == Type::ACTION_DETAILED || $goal->type_id == Type::PARENT_GOAL)
        {
            $nav_show .= '|shift-dates';
        }

        if($goal->type_id == Type::FUTURE_GOAL)
        {
            $nav_show .= '|edit|convert-active';
        }
        else
        {
            if($goal->achieved)
            {
                $nav_show .= '|toggle-unachieved';
            }
            else
            {
                $nav_show .= '|edit|toggle-achieved';
            }
        }

        if($goal->type_id == Type::MANUAL_GOAL)
        {
            $nav_show .= '|update-manual-progress';
        }

        if(!is_null($goal->parent_id))
        {
            $nav_show = str_replace('back', 'parent-back', $nav_show);
            $nav_show .= '|remove-parent';
        }
        else
        {
            $nav_show .= '|convert-sub';
        }

        // Build dropdown nav
        $dropdown_nav = [
            'details' => 'Details',
        ];

        if($goal->type_id != Type::FUTURE_GOAL)
        {
            $dropdown_nav['progress'] = 'Progress';
            $dropdown_nav['show-all'] = 'Show All';
        }

        if($goal->type_id == Type::PARENT_GOAL)
        {
            $dropdown_nav['sub-goals'] = 'Sub Goals';
        }

        if(in_array($goal->type_id, [Type::ACTION_DETAILED, Type::ACTION_AD_HOC, Type::BUCKETLIST]))
        {
            $dropdown_nav['action-plan'] = 'Action Plan';
        }

        if($goal->type_id == Type::ACTION_AD_HOC || $goal->type_id == Type::BUCKETLIST)
        {
            $dropdown_nav['ad-hoc-list'] = 'Ad Hoc List';
        }

        $selected_dropdown = null;
        if($request->has('selected-dropdown'))
        {
            $selected_dropdown = $request->get('selected-dropdown');
        }

        // Load extra info needed for view
        $goal->load('category', 'status');

        if($goal->type_id == Type::PARENT_GOAL)
        {
            $goal->load('subGoals');
        }

        if($goal->type_id == Type::ACTION_DETAILED || $goal->type_id == Type::ACTION_AD_HOC)
        {
            $goal->load('actionItems');
        }
        elseif($goal->type_id == Type::BUCKETLIST)
        {
            $goal->loadBucketlistActionItems();
        }

        if($goal->type_id == Type::ACTION_AD_HOC)
        {
            $goal->load('adHocItems');
        }
        elseif($goal->type_id == Type::BUCKETLIST)
        {
            $goal->loadBucketlistAdHocItems();
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
            'selected_dropdown' => $selected_dropdown,
            'status' => Status::class,
            'type' => Type::class,
            'user' => $user,
            'setting' => Setting::class,
        ]);
    }

    public function viewActionItem(Request $request, GoalActionItem $action_item)
    {
        // Load goal for nav and forms
        $action_item->load('goal');

        // Load reminders
        $action_item->load('reminders');

        // Build nav
        $show = 'back-goal|reminders|delete';
        if($action_item->achieved)
        {
            $show .= '|toggle-unachieved';
        }
        else
        {
            if($action_item->goal->type_id == Type::ACTION_AD_HOC)
            {
                $show .= '|edit';

                if(!is_null($action_item->deadline))
                {
                    $show .= '|toggle-achieved|clear-deadline';
                }
            }
            else
            {
                $show .= '|edit|toggle-achieved';
            }
        }

        // Return detail view
        return view('goals.action-item-details')->with([
            'action_item' => $action_item,
            'show' => $show,
        ]);
    }

    public function viewBucketlistItem(Request $request, BucketlistItem $bucketlist_item, Goal $goal = null)
    {
        // If it's not assigned to a goal, redirect to the bucketlist view
        if(is_null($bucketlist_item->goal_id) && is_null($goal))
        {
            return redirect()->route('bucketlist.view.details', ['bucketlist_item' => $bucketlist_item->uuid]);
        }

        // Load goal for nav and forms
        if(!is_null($goal))
        {
            $bucketlist_item->goal = $goal;
        }
        else
        {
            $bucketlist_item->load('goal');
        }

        // Set reminders
        $bucketlist_item->reminders = array();

        // Build nav
        $show = 'back-goal';
        if($bucketlist_item->achieved)
        {
            $show .= '|toggle-unachieved';
        }
        elseif(!is_null($bucketlist_item->deadline))
        {
            $show .= '|toggle-achieved|clear-deadline';
        }

        // Return detail view
        return view('goals.action-item-details')->with([
            'action_item' => $bucketlist_item,
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

    public function clearAdHocDeadline(Request $request, GoalActionItem $action_item)
    {
        // Clear deadline
        $action_item->load('goal');
        if($action_item->goal->type_id == Type::ACTION_AD_HOC)
        {
            $action_item->deadline = null;
        }

        // Save
        if(!$action_item->save())
        {
            // Log error
            Log::error('Failed to clear action item deadline', $action_item->toArray());
        }

        // Return detail view
        return redirect()->route('goals.view.action-item', ['action_item' => $action_item->uuid]);
    }

    public function setAdHocDeadline(SetDeadlineRequest $request, GoalActionItem $action_item)
    {
        // Set deadline
        $action_item->deadline = $request->get('deadline');

        // Save
        if(!$action_item->save())
        {
            // Log error
            Log::error('Failed to set action item deadline', $action_item->toArray());
        }

        // Redirect
        $view_details = false;
        if($request->has('view_details'))
        {
            $view_details = (bool) $request->get('view_details');
        }

        if($view_details)
        {
            return redirect()->route('goals.view.action-item', ['action_item' => $action_item->uuid]);
        }

        $action_item->load('goal');
        return redirect()->route('goals.view.goal', ['goal' => $action_item->goal->uuid, 'selected-dropdown' => 'ad-hoc-list']);
    }

    public function clearBucketlistDeadline(Request $request, BucketlistItem $bucketlist_item)
    {
        // Clear deadline
        $bucketlist_item->load('goal');
        $bucketlist_item->deadline = null;
        $bucketlist_item->goal_id = null;

        // Save
        if(!$bucketlist_item->save())
        {
            // Log error
            Log::error('Failed to clear bucketlist item deadline', $bucketlist_item->toArray());
        }

        // Return detail view
        return redirect()->route('goals.view.bucketlist-item', ['bucketlist_item' => $bucketlist_item->uuid, 'goal' => $bucketlist_item->goal->uuid]);
    }

    public function setBucketlistDeadline(SetDeadlineRequest $request, BucketlistItem $bucketlist_item, Goal $goal)
    {
        // Set deadline
        $bucketlist_item->deadline = $request->get('deadline');
        $bucketlist_item->goal_id = $goal->id;

        // Save
        if(!$bucketlist_item->save())
        {
            // Log error
            Log::error('Failed to set bucketlist item deadline', $bucketlist_item->toArray());
        }

        // Redirect
        $view_details = false;
        if($request->has('view_details'))
        {
            $view_details = (bool) $request->get('view_details');
        }

        if($view_details)
        {
            return redirect()->route('goals.view.bucketlist-item', ['bucketlist_item' => $bucketlist_item->uuid]);
        }

        return redirect()->route('goals.view.goal', ['goal' => $goal->uuid, 'selected-dropdown' => 'ad-hoc-list']);
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
        if(($type_id == Type::ACTION_AD_HOC || $type_id == Type::BUCKETLIST) && $request->has('custom-times'))
        {
            $goal->custom_times = $request->get('custom-times');
            if($request->has('time-period'))
            {
                $goal->time_period_id = $request->get('time-period');
            }
            else
            {
                $goal->time_period_id = TimePeriod::TOTAL;
            }
        }

        // Manual options
        if($type_id == Type::MANUAL_GOAL && $request->has('custom-times'))
        {
            $goal->custom_times = $request->get('custom-times');
            $goal->manual_completed = 0;
        }

        // Habit options
        $calculate_habit_progress = false;
        if($type_id == Type::HABIT_BASED && !is_null($habit_strength) && !is_null($habit))
        {
            $goal->habit_strength = $habit_strength;
            $goal->habit_id = $habit->id;
            $calculate_habit_progress = true;
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

        if(!$goal->save())
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

        // Calculate strength if it's a habit based goal
        if($calculate_habit_progress)
        {
            if(!$goal->calculateProgress())
            {
                Log::error("Failed to calculate progress after storing habit based goal.", $goal->toArray());
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
        else
        {
            // Recalculate progress with the new item
            $goal->calculateProgress();
        }

        if($goal->type_id == Type::ACTION_DETAILED)
        {
            $selected_dropdown = 'action-plan';
        }
        else // ad hoc
        {
            $selected_dropdown = 'ad-hoc-list';
        }

        return redirect()->route('goals.view.action-item', ['action_item' => $action_item->uuid, 'selected-dropdown' => $selected_dropdown]);
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
        if($goal->achieved)
        {
            return redirect()->route('goals.view.goal', ['goal' => $goal->uuid]);
        }

        return view('goals.edit')->with(['goal' => $goal]);
    }

    public function editActionItem(Request $request, GoalActionItem $action_item)
    {
        if($action_item->achieved)
        {
            return redirect()->route('goals.view.action-item', ['action_item' => $action_item->uuid]);
        }
        
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
            if(!$goal->shiftDates($days, 'sub'))
            {
                Log::error('Failed to shift dates for goal', [
                    'goal' => $goal->toArray(),
                    'days' => $days,
                    'request_values' => $request->all(),
                ]);
            }
        }
        elseif($days > 0)
        {
            if(!$goal->shiftDates($days))
            {
                Log::error('Failed to shift dates for goal', [
                    'goal' => $goal->toArray(),
                    'days' => $days,
                    'request_values' => $request->all(),
                ]);
            }
        }

        return redirect()->route('goals.view.goal', ['goal' => $goal->uuid]);
    }

    public function updateGoal(UpdateRequest $request, Goal $goal)
    {
        if($goal->achieved)
        {
            return redirect()->route('goals.view.goal', ['goal' => $goal->uuid]);
        }
        
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
        if(($goal->type_id == Type::ACTION_AD_HOC || $goal->type_id == Type::MANUAL_GOAL) && $request->get('custom-times'))
        {
            $goal->custom_times = $request->get('custom-times');
        }

        if($goal->type_id == Type::ACTION_AD_HOC && $request->get('time-period'))
        {
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

        if(!$goal->save())
        {
            // Log error
            Log::error('Failed to update goal.', [
                'user->id' => $user->id,
                'goal' => $goal->toArray(),
                'request_values' => $request->all(),
            ]);
        }
        else
        {
            if(!$goal->calculateProgress())
            {
                // Log error
                Log::error('Failed to calculate progress for goal after updating goal details', $goal->toArray());
            }
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
        if($action_item->achieved)
        {
            return redirect()->route('goals.view.action-item', ['action_item' => $action_item->uuid]);
        }
        
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

        $action_item->load('goal');
        if($action_item->goal->type_id == Type::ACTION_DETAILED || !is_null($action_item->deadline))
        {
            $selected_dropdown = 'action-plan';
        }
        else // ad hoc
        {
            $selected_dropdown = 'ad-hoc-list';
        }

        return redirect()->route('goals.view.action-item', ['action_item' => $action_item->uuid, 'selected-dropdown' => $selected_dropdown]);
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
        return redirect()->route('goals.view.goal', ['goal' => $goal->uuid, 'selected-dropdown' => 'progress']);
    }

    public function destroyCategory(Request $request, GoalCategory $category)
    {
        // Remove category from goals with that category
        Goal::where('user_id', $request->user()->id)->where('category_id', $category->id)->update(['category_id' => null]);

        // Delete category
        if(!$category->delete())
        {
            Log::error('Failed to delete goal category', $category->toArray());
            return redirect()->back();
        }

        return redirect()->route('goals.edit.categories');
    }

    public function destroyGoal(Goal $goal)
    {
        // Delete any associated to-dos
        $goal->load('actionItems');
        if(!is_null($goal->actionItems))
        {
            foreach($goal->actionItems as $action_item)
            {
                $action_item->load('todo');
                if(!is_null($action_item->todo))
                {
                    if(!$action_item->todo->delete())
                    {
                        Log::error('Failed to delete To-Do item associated with goal action item when deleting goal.', $action_item->toArray());
                    }
                }
            }
        }

        // Delete goal
        if(!$goal->delete())
        {
            Log::error('Failed to delete goal', $goal->toArray());
            return redirect()->back();
        }

        if(!is_null($goal->parent_id))
        {
            $goal->load('parent');
            return redirect()->route('goals.view.goal', [
                'goal' => $goal->parent->uuid,
                'selected-dropdown' => 'sub-goals',
            ]);
        }
        elseif($goal->type_id == Type::FUTURE_GOAL)
        {
            return redirect()->route('goals', ['scope' => 'future']);
        }

        return redirect()->route('goals');
    }

    public function destroyActionItem(GoalActionItem $action_item)
    {
        // Delete associated To-Do item
        $action_item->load('todo');
        if(!is_null($action_item->todo))
        {
            if(!$action_item->todo->delete())
            {
                Log::error('Failed to delete To-Do item associated with deleted action item.', $action_item->toArray());
            }
        }

        // Delete action item
        if(!$action_item->delete())
        {
            Log::error('Failed to delete goal action item.', $action_item->toArray());
            return redirect()->back();
        }

        // Load goal for redirect
        $action_item->load('goal');

        // Set selected dropdown
        if($action_item->goal->type_id == Type::ACTION_DETAILED || !is_null($action_item->deadline))
        {
            $selected_dropdown = 'action-plan';
        }
        else // ad hoc
        {
            $selected_dropdown = 'ad-hoc-list';
        }

        return redirect()->route('goals.view.goal', ['goal' => $action_item->goal->uuid, 'selected-dropdown' => $selected_dropdown]);
    }

    public function removeParent(Goal $goal)
    {
        $goal->parent_id = null;

        if(!$goal->save())
        {
            Log::error('Failed to remove parent from goal', $goal->toArray());
            return redirect()->back();
        }

        return redirect()->route('goals.view.goal', ['goal' => $goal->uuid]);
    }

    public function convertSubForm(Request $request, Goal $goal)
    {
        $parent_goals = Goal::where('user_id', $request->user()->id)->where('type_id', Type::PARENT_GOAL)->where('id', '!=', $goal->id)->orderBy('name')->get();
        
        return view('goals.parent-selector')->with([
            'parent_goals' => $parent_goals,
            'goal' => $goal,
        ]);
    }

    public function convertSubSubmit(ConvertSubRequest $request, Goal $goal)
    {
        $parent_goal_uuid = $request->get('parent-goal');
        if($parent_goal_uuid != $goal->uuid)
        {
            $goal->parent_id = Goal::where('uuid', $parent_goal_uuid)->first()->id;

            if(!$goal->save())
            {
                Log::error('Failed convert goal to sub-goal', [
                    'goal' => $goal->toArray(),
                    'parent_goal_uuid' => $parent_goal_uuid,
                ]);
                return redirect()->back();
            }
        }

        return redirect()->route('goals.view.goal', ['goal' => $goal->uuid]);
    }

    public function transferAdHocItemsForm(Request $request, Goal $goal)
    {
        $ad_hoc_goals = Goal::where('user_id', $request->user()->id)->where('type_id', Type::ACTION_AD_HOC)->where('id', '!=', $goal->id)->orderBy('name')->get();
        
        return view('goals.ad-hoc-selector')->with([
            'ad_hoc_goals' => $ad_hoc_goals,
            'goal' => $goal,
        ]);
    }

    public function transferAdHocItemsSubmit(TransferAdHocRequest $request, Goal $goal)
    {
        $ad_hoc_goal_uuid = $request->get('ad-hoc-goal');
        if($ad_hoc_goal_uuid != $goal->uuid)
        {
            // Get action items for the old goal
            $goal->load('adHocItems');

            // Get goal to transfer ad hoc items to
            $ad_hoc_goal = Goal::where('uuid', $ad_hoc_goal_uuid)->first();

            // Assign all ad hoc items to new goal
            $failures = array();
            foreach($goal->adHocItems as $ad_hoc_item)
            {
                $ad_hoc_item->goal_id = $ad_hoc_goal->id;
                if(!$ad_hoc_item->save())
                {
                    array_push($failures, $ad_hoc_item->id);
                    Log::error('Failed convert goal to sub-goal', [
                        'goal' => $goal->toArray(),
                        'parent_goal_uuid' => $parent_goal_uuid,
                    ]);
                }
            }

            // Log failures
            if(count($failures) > 0)
            {
                Log::error('Failed move some ad hoc items to new goal.', [
                    'goal' => $goal->toArray(),
                    'ad_hoc_goal' => $ad_hoc_goal->toArray(),
                    'failures' => $failures,
                ]);
            }
        }

        return redirect()->route('goals.view.goal', ['goal' => $ad_hoc_goal->uuid]);
    }

    public function editReminders(GoalActionItem $action_item)
    {
        // Load reminders
        $action_item->load('reminders');

        // Return edit reminders page
        return view('goals.reminders')->with([
            'action_item' => $action_item,
        ]);
    }

    public function storeReminder(StoreReminderRequest $request, GoalActionItem $action_item)
    {
        // Get user timezone
        $timezone = $request->user()->timezone ?? 'America/Denver';

        // Create carbon obj for remind at
        $carbon = Carbon::createFromFormat('Y-m-d H:i', $request->get('date') . ' ' . $request->get('time'), $timezone)->setTimezone('UTC');

        // Check for exsisting reminder
        $reminder = GoalActionItemReminder::where('action_item_id', $action_item->id)->where('remind_at', $carbon->toDatetimeString())->first();
        if(!is_null($reminder))
        {
            return redirect()->back()->withErrors([
                'date' => 'Reminder already exists',
            ]);
        }
        elseif($carbon->lessThan(Carbon::now())) // Verify reminder is in the future
        {
            return redirect()->back()->withErrors([
                'date' => 'Reminder must be in the future',
            ]);
        }

        // Create reminder
        $reminder = new GoalActionItemReminder([
            'action_item_id' => $action_item->id,
            'remind_at' => $carbon->toDatetimeString(),
        ]);

        // Save and log errors
        if(!$reminder->save())
        {
            Log::error('Failed to save goal action item reminder.', [
                'action_item' => $action_item->toArray(),
                'reminder' => $reminder->toArray(),
                'request_values' => $request->all(),
            ]);    
        }

        return redirect()->route('goals.edit.reminders', ['action_item' => $action_item->uuid]);
    }

    public function destroyReminder(GoalActionItemReminder $reminder)
    {
        // Load todo item
        $reminder->load('actionItem');
        $action_item = $reminder->actionItem;

        // Delete reminder
        if(!$reminder->delete())
        {
            Log::error('Failed to delete goal action item reminder', $reminder->toArray());
            return redirect()->back();
        }

        return redirect()->route('goals.edit.reminders', ['action_item' => $action_item->uuid]);
    }

    private function recursivelyUpdateGoalsStatus(&$goals, $refresh_goals)
    {
        foreach($goals as $goal)
        {
            if(!$goal->determineStatus())
            {
                Log::error('Failed to recursively determine status for goal.', $goal->toArray());
            }
            elseif($refresh_goals)
            {
                $goal->refresh();
            }

            // Refresh sub goals
            $goal->load('subGoals');
            if(!is_null($goal->subGoals) && $goal->subGoals->count() > 0)
            {
                $this->recursivelyUpdateGoalsStatus($goal->subGoals, false);
            }
        }
    }
}
