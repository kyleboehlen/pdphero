<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;

// Constants
use App\Helpers\Constants\ToDo\Type;
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\ToDo\ToDo;

// Requests
use App\Http\Requests\ToDo\CreateRequest;
use App\Http\Requests\ToDo\StoreRequest;
use App\Http\Requests\ToDo\UpdateRequest;

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
    public function index(Request $request)
    {
        // Get logged in user
        $user = $request->user();

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
            Log::error('Failed to update new To-Do item.', [
                'todo' => $todo->toArray(),
                'request_values' => $request->all(),
            ]);

            // Redirect back with old values and error
            return redirect()->back()->withInput($request->input())->withErrors([
                'error' => 'Something went wrong updating To-Do item, please try again.'
            ]);
        }

        return redirect()->route('todo.list');
    }

    public function destroy(ToDo $todo)
    {
        if(!$todo->delete())
        {
            Log::error('Failed to delete to-do item', $todo->toArray());
            return redirect()->back();
        }

        return redirect()->route('todo.list');
    }

    public function toggleCompleted(ToDo $todo)
    {
        if(!$todo->toggleCompleted())
        {
            // Log error
            Log::error('Failed to toggle completed on to-do item', ['uuid' => $todo->uuid]);
            return redirect()->back();
        }

        return redirect()->route('todo.list');
    }

    public function colorGuide()
    {
        return view('todo.colors');
    }
}
