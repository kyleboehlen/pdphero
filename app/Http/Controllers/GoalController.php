<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Constants
use App\Helpers\Constants\Goal\Type;

// Models
use App\Models\Goal\Goal;
use App\Models\Goal\GoalActionItem;
use App\Models\Goal\GoalCategory;

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
        $goals = Goal::where('user_id', $user->id);

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

    public function createGoal(Request $request, Goal $goal)
    {

    }

    public function createActionItem(Request $request, GoalActionItem $action_item)
    {

    }

    public function storeGoal(Request $request, Goal $goal)
    {

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
