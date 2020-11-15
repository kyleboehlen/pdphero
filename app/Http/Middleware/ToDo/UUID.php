<?php

namespace App\Http\Middleware\ToDo;

use Closure;
use Illuminate\Http\Request;

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
            $user = \Auth::user(); // Get logged in user
            if($todo->user_id != $user->id) // Verify to do item belongs to user
            {
                return abort(403); // Return forbidden if different user's To-Do item
            }
        }
        return $next($request);
    }
}
