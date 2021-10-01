<?php

namespace App\Http\Middleware\Addiction;

use Closure;
use Illuminate\Http\Request;

class MilestoneUUID
{
    /**
     * Verifies that any addiction milestone UUIDs in the URL
     * string belong to the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if UUID is being passed in url string
        if(!is_null($addiction_milestone = $request->route('addiction_milestone')))
        {
            // Verify milestone belongs to user
            $addiction_ids = $request->user()->addictions->pluck('id')->toArray();
            if(!in_array($addiction_milestone->addiction_id, $addiction_ids))
            {
                return abort(403); // Return forbidden if different user's milestone
            }
        }
        return $next($request);
    }
}
