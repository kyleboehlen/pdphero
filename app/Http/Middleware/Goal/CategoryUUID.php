<?php

namespace App\Http\Middleware\Goal;

use Closure;
use Illuminate\Http\Request;

class CategoryUUID
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
        if(!is_null($category = $request->route('category')))
        {
            if($category->user_id != $request->user()->id) // Verify category belongs to user
            {
                return abort(403); // Return forbidden if different user's category 
            }
        }
        return $next($request);
    }
}
