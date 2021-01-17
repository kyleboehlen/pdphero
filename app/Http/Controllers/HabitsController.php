<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\Habits\Habits;

class HabitsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('habits.uuid');
        $this->middleware('verified');
        // To-do: Add subscription middleware
    }

    public function index(Request $request)
    {
        // Get logged in user
        $user = $request->user();

        // Load user's habits
        $habits = Habits::where('user_id', $user->id)->orderBy('type_id')->orderBy('custom_order')->orderBy('name')->get();

        // Return habit view
        return view('habits.index')->with([
            'habits' => $habits,
        ]);
    }
}
