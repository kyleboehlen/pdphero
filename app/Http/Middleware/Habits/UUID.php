<?php

namespace App\Http\Middleware\Habits;

use Closure;
use Illuminate\Http\Request;

class UUID
{
    /**
     * Verifies that any affirmation UUIDs in the URL
     * string belong to the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if UUID is being passed in url string
        if(!is_null($habit = $request->route('habit')))
        {
            if($habit->user_id != $request->user()->id) // Verify habit belongs to user
            {
                return abort(403); // Return forbidden if different user's affirmation
            }
        }
        return $next($request);
    }
}
