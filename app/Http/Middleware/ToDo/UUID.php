<?php

namespace App\Http\Middleware\ToDo;

use Closure;
use Illuminate\Http\Request;

// Constants
use App\Helpers\Constants\ToDo\Type;

class UUID
{
    /**
     * Verifies that any To-Do item UUIDs in the URL
     * string belong to the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if UUID is being passed in url string
        if(!is_null($todo = $request->route('todo')))
        {
            if($todo->user_id != $request->user()->id) // Verify to do item belongs to user
            {
                return abort(403); // Return forbidden if different user's To-Do item
            }

            // Get route name to ban certain todos from certain routes
            $route_name = $request->route()->getName();

            // Ban routes based on todo being completed
            if($todo->completed)
            {
                // Ban it from the update and edit routes
                if($route_name == 'todo.edit' || $route_name == 'todo.update' || $route_name == 'todo.update.habit')
                {
                    return abort(403);
                }
            }

            // Ban routes based on todo type
            if($todo->type_id == Type::TODO_ITEM) // if todo item is a, well, user created todo item
            {
                // Ban it from the update habit route
                if($route_name == 'todo.update.habit')
                {
                    return abort(403);
                }
            }
            elseif($todo->type_id == Type::RECURRING_HABIT_ITEM) // If todo item is a recurring habit
            {
                // Ban it from the update and destroy routes
                if($route_name == 'todo.update' || $route_name == 'todo.destroy')
                {
                    return abort(403);
                }
            }
            elseif($todo->type_id == Type::SINGULAR_HABIT_ITEM)
            {
                // Ban it from the update route
                if($route_name == 'todo.update')
                {
                    return abort(403);
                }
            }
        }
        return $next($request);
    }
}
