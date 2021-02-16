<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\Goal\Goal;
use App\Models\Goal\GoalActionItem;

class GoalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('goal.action_item.uuid');
        $this->middleware('goal.uuid');
        $this->middleware('verified');
        // To-do: Add subscription middleware
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

    }

    public function destroyActionItem(Request $request, GoalActionItem $action_item)
    {

    }
}
