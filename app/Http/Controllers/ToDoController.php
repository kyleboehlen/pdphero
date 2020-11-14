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
        $to_do_items =
            Todo::where('user_id', $user->id)->with('priority')->orderBy('completed')->orderBy('priority_id', 'desc')->orderBy('title')->get(); // This is going to need to be rewritten with constrained eager loads: https://laravel.com/docs/8.x/eloquent-relationships#constraining-eager-loads

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

    public function edit(EditRequest $request, ToDo $todo)
    {

    }

    public function update(UpdateRequest $request, ToDo $todo)
    {

    }

    public function destroy(DestroyRequest $request, ToDo $todo)
    {
        if(!$todo->delete())
        {
            
        }

        return redirect()->route('todo');
    }
}
