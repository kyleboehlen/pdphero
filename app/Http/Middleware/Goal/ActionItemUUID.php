<?php

namespace App\Http\Middleware\Goal;

use Closure;
use Illuminate\Http\Request;

class ActionItemUUID
{
    /**
     * Verifies that any goal action item UUIDs in the URL
     * string belong to the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if UUID is being passed in url string
        if(!is_null($action_item = $request->route('action_item')))
        {
            // Verify goal belongs to user
            $goal_ids = $request->user()->goals->pluck('id')->toArray();
            if(!in_array($action_item->goal_id, $goal_ids))
            {
                return abort(403); // Return forbidden if different user's goal
            }
        }
        return $next($request);
    }
}
