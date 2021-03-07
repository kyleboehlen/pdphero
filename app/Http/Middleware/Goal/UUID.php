<?php

namespace App\Http\Middleware\Goal;

use Closure;
use Illuminate\Http\Request;

// Constants
use App\Helpers\Constants\Goal\Type;

class UUID
{
    /**
     * Verifies that any goal UUIDs in the URL
     * string belong to the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if UUID is being passed in url string
        if(!is_null($goal = $request->route('goal')))
        {
            if($goal->user_id != $request->user()->id) // Verify goal belongs to user
            {
                return abort(403); // Return forbidden if different user's goal
            }
        }

        // Check if parent UUID is being passed in url string
        if(!is_null($goal = $request->route('parent_goal')))
        {
            if($goal->user_id != $request->user()->id) // Verify goal belongs to user
            {
                return abort(403); // Return forbidden if different user's goal
            }
            elseif($goal->type_id != Type::PARENT_GOAL) // Verify it actually is a parent goal
            {
                return abort(403); // Return forbidden
            }
        }
        return $next($request);
    }
}
