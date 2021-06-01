<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;

// Constants
use App\Helpers\Constants\ToDo\Type;
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\Habits\Habits;
use App\Models\Relationships\HabitsToDo;
use App\Models\Relationships\GoalActionItemsToDo;
use App\Models\ToDo\ToDo;

// Requests
use App\Http\Requests\ToDo\StoreRequest;
use App\Http\Requests\ToDo\StoreHabitRequest;
use App\Http\Requests\ToDo\UpdateRequest;
use App\Http\Requests\ToDo\UpdateHabitRequest;

class ToDoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('first_visit.messages');
        $this->middleware('todo.uuid');
        $this->middleware('verified');
        $this->middleware('membership');
    }

    /**
     * Home To-Do page
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // Get logged in user
        $user = $request->user();

        // Build users habit todos
        $build_habit_todos = buildRecurringHabitToDos($user);
        if($build_habit_todos !== true)
        {
            // Log error
            Log::error('Failed to build habit to-do items for user.', [
                'user->id' => $user->id,
                '# of failures' => $build_habit_todos,
            ]);
        }

        // Build users action item todos
        $build_action_item_todos = buildActionItemTodos($user);
        if($build_action_item_todos !== true)
        {
            // Log error
            Log::error('Failed to build action item to-do items for user.', [
                'user->id' => $user->id,
                '# of failures' => $build_action_item_todos,
            ]);
        }

        // Load user's to-do items
        $to_do_items = Todo::where('user_id', $user->id)->with('priority'); // This is going to need to be rewritten with constrained eager loads (habits/goals): https://laravel.com/docs/8.x/eloquent-relationships#constraining-eager-loads

        // Constrain by how far back user wants to see completed to do items
        $completed_at = Carbon::now()->subHours($user->getSettingValue(Setting::TODO_SHOW_COMPLETED_FOR))->toDatetimeString();
        $to_do_items = $to_do_items->where(function($q) use ($completed_at){
            $q->where('completed', 0)->orWhere(function($s_q) use ($completed_at){ // Is either incomplete
                $s_q->where('completed', 1)->where('updated_at', '>=', $completed_at); // or is complete and within the hours to display completed for user
            });
        });

        // Check if user wants completed to-do items to move to the bottom of the list
        if((bool) $user->getSettingValue(Setting::TODO_MOVE_COMPLETED))
        {
            // If so, order by completed first
            $to_do_items = $to_do_items->orderBy('completed');
        }
        
        // Default ordering
        $to_do_items = $to_do_items->orderBy('priority_id', 'desc')->orderBy('title')->get();

        // Return to-do view
        return view('todo.list')->with([
            'to_do_items' => $to_do_items,
            'user' => $user,
            'setting' => Setting::class,
        ]);
    }

    public function viewDetails(ToDo $todo)
    {
        // Return the completed view if to-do item is completed
        if($todo->completed)
        {
            return view('todo.completed')->with([
                'item' => $todo,
            ]);
        }

        // Return view details page
        return view('todo.details')->with([
            'item' => $todo,
            'type' => Type::class,
        ]);
    }

    public function create()
    {
        // Return the create to-do item form
        return view('todo.create');
    }

    public function createHabit(Request $request)
    {
        // Return the create to-do item form
        return view('todo.create')->with([
            'create_type' => Type::SINGULAR_HABIT_ITEM,
        ]);
    }

    public function store(StoreRequest $request)
    {
        // Create new to-do
        $todo = new Todo();

        // Set type to normal to-do item
        $todo->type_id = Type::TODO_ITEM;

        // Set user
        $user = \Auth::user();
        $todo->user_id = $user->id;

        // Set title
        $todo->title = $request->get('title');

        // Set priority
        foreach(config('todo.priorities') as $id => $priority)
        {
            if($request->has("priority-$id"))
            {
                $todo->priority_id = $id;
            }
        }

        // Set notes
        $todo->notes = $request->get('notes');

        if(!$todo->save())
        {
            // Log error
            Log::error('Failed to store new To-Do item.', [
                'user->id' => $user->id,
                'todo' => $todo->toArray(),
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to create To-Do item, please try again.'
            ]);
        }

        return redirect()->route('todo.list');
    }

    public function storeHabit(StoreHabitRequest $request)
    {
        // Get the habit
        $habit = Habits::where('uuid', $request->get('habit'))->first();

        // Create new to-do
        $todo = new Todo();

        // Set type to singular habit item
        $todo->type_id = Type::SINGULAR_HABIT_ITEM;

        // Set user
        $user = \Auth::user();
        $todo->user_id = $user->id;

        // Set title
        $todo->title = $habit->name;

        // Set priority
        foreach(config('todo.priorities') as $id => $priority)
        {
            if($request->has("priority-$id"))
            {
                $todo->priority_id = $id;
            }
        }

        // Set notes
        $todo->notes = $request->get('notes');

        // If notes aren't set, pull from habit's notes
        if(is_null($todo->notes))
        {
            $todo->notes = $habit->notes;
        }

        if(!$todo->save())
        {
            // Log error
            Log::error('Failed to store new Singular Habit To-Do item.', [
                'user->id' => $user->id,
                'todo' => $todo->toArray(),
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to create To-Do item, please try again.'
            ]);
        }

        // Associate it with a habit
        if(!HabitsToDo::create([
            'habits_id' => $habit->id,
            'to_do_id' => $todo->id,
        ]))
        {
            // Log error
            Log::error('Failed to associate Singular Habit To-Do item with a habit.', [
                'habits_id' => $habit->id,
                'to_do_id' => $todo->id,
            ]);

            // Delete the new todo
            if(!$todo->delete())
            {
                Log::critical('Failed to delete Singular Habit To-Do item after failing to associate it with a habit.', [
                    'habits_id' => $habit->id,
                    'to_do_id' => $todo->id,
                ]);
            }

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to create To-Do item, please try again.'
            ]);
        }

        return redirect()->route('todo.list');
    }

    public function edit(ToDo $todo)
    {
        // Return view to edit title, pri, notes with the todo item
        return view('todo.edit')->with([
            'item' => $todo,
            'type' => Type::class,
        ]);
    }

    public function update(UpdateRequest $request, ToDo $todo)
    {
        // Set title
        if($todo->type_id == Type::TODO_ITEM)
        {
            $todo->title = $request->get('title');
        }

        // Set priority
        foreach(config('todo.priorities') as $id => $priority)
        {
            if($request->has("priority-$id"))
            {
                $todo->priority_id = $id;
            }
        }

        // Set notes
        $todo->notes = $request->get('notes');

        if(!$todo->save())
        {
            // Log error
            $user = \Auth::user();
            Log::error('Failed to update To-Do item.', [
                'todo' => $todo->toArray(),
                'request_values' => $request->all(),
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong updating To-Do item, please try again.'
            ]);
        }

        return redirect()->route('todo.view.details', ['todo' => $todo->uuid]);
    }

    public function updateHabit(UpdateHabitRequest $request, ToDo $todo)
    {
        // Set priority
        foreach(config('todo.priorities') as $id => $priority)
        {
            if($request->has("priority-$id"))
            {
                $todo->priority_id = $id;
            }
        }

        // Set notes
        $todo->notes = $request->get('notes');

        if(!$todo->save())
        {
            // Log error
            $user = \Auth::user();
            Log::error('Failed to update recurring habit To-Do item.', [
                'todo' => $todo->toArray(),
                'request_values' => $request->all(),
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong updating To-Do item, please try again.'
            ]);
        }

        return redirect()->route('todo.view.details', ['todo' => $todo->uuid]);
    }

    public function destroy(ToDo $todo)
    {
        if($todo->type_id != Type::RECURRING_HABIT_ITEM)
        {
            if(!$todo->delete())
            {
                Log::error('Failed to delete to-do item', $todo->toArray());
                return redirect()->back();
            }
    
            // Delete the relationship if it is an action item
            if($todo->type_id == Type::ACTION_ITEM)
            {
                if(!GoalActionItemsToDo::where('to_do_id', $todo->id)->delete())
                {
                    Log::error('Failed to delete action item relationship after deleting to-do item', $todo->toArray());
                }
            }
        }

        return redirect()->route('todo.list');
    }

    public function toggleCompleted(ToDo $todo, $view_details = false)
    {
        if(!$todo->toggleCompleted())
        {
            // Log error
            Log::error('Failed to toggle completed on to-do item', ['uuid' => $todo->uuid]);
            return redirect()->back();
        }

        if($view_details)
        {
            return redirect()->route('todo.view.details', ['todo' => $todo->uuid]);
        }

        return redirect()->route('todo.list');
    }

    public function colorGuide()
    {
        return view('todo.colors');
    }
}
