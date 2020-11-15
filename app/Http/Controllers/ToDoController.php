<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

// Models
use App\Models\ToDo\ToDo;

// Requests


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

    }

    public function store(StoreRequest $request)
    {

    }

    public function edit(ToDo $todo)
    {
        // Make sure you can't edit to-dos that are completed, not quite sure how to handle this yet
    }

    public function update(UpdateRequest $request, ToDo $todo)
    {

    }

    public function destroy(ToDo $todo)
    {
        if(!$todo->delete())
        {
            
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
