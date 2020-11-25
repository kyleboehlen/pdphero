<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

// Constants
use App\Helpers\Constants\ToDo\Type;

// Models
use App\Models\ToDo\ToDo;

// Requests
use App\Http\Requests\ToDo\CreateRequest;
use App\Http\Requests\ToDo\StoreRequest;

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
        $this->middleware('todo.uuid');
        $this->middleware('verified');
        // To-do: Add subscription middleware
    }

    /**
     * Home To-Do page
     *
     * @return Response
     */
    public function index()
    {
        // Get logged in user
        $user = \Auth::user();

        // Load user's to-do items
        $to_do_items = Todo::where('user_id', $user->id)->with('priority'); // This is going to need to be rewritten with constrained eager loads (habits/goals): https://laravel.com/docs/8.x/eloquent-relationships#constraining-eager-loads

        // To-do: Add a setting to decide how long to show to-dos after completion, constrain updated_at by this

        if(true) // To-Do: add setting for moving completed to-dos to the bottom or not
        {
            $to_do_items = $to_do_items->orderBy('completed');
        }
        
        // Default ordering
        $to_do_items = $to_do_items->orderBy('priority_id', 'desc')->orderBy('title')->get();

        // Return to-do view
        return view('todo.list')->with([
            'to_do_items' => $to_do_items,
        ]);
    }

    public function create(CreateRequest $request)
    {
        // Return the create to-do item form
        return view('todo.create')->with([
            'from' => $request->get('from')
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
            $user = \Auth::user();
            Log::error('Failed to store new To-Do item.', [
                'user_id' => $user->id,
                'todo' => $todo->toArray(),
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong trying to create To-Do item, please try again.'
            ]);
        }

        return redirect()->route('todo.list');
    }

    // To-Do: storeFromHabit() and storeFromGoal()

    public function edit(ToDo $todo)
    {
        // Return the completed view if to-do item is completed
        if($todo->completed)
        {
            return view('todo.completed')->with([
                'item' => $todo,
            ]);
        }

        // Return view to edit title, pri, notes with the todo item
        return view('todo.edit')->with([
            'item' => $todo,
            'type' => Type::class,
        ]);
    }

    public function update(UpdateRequest $request, ToDo $todo)
    {

    }

    public function destroy(ToDo $todo)
    {
        if(!$todo->delete())
        {
            Log::error('Failed to toggle completed on to-do item', ['uuid' => $todo->uuid]);
            return redirect()->back();
        }

        return redirect()->route('todo.list');
    }

    public function toggleCompleted(ToDo $todo)
    {
        if(!$todo->toggleCompleted())
        {
            return redirect()->back();
        }

        return redirect()->route('todo.list');
    }
}
