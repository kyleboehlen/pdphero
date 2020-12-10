<?php

namespace App\Http\Middleware\Affirmations;

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
        if(!is_null($affirmation = $request->route('affirmation')))
        {
            if($affirmation->user_id != $request->user()->id) // Verify affirmation belongs to user
            {
                return abort(403); // Return forbidden if different user's affirmation
            }
        }
        return $next($request);
    }
}
