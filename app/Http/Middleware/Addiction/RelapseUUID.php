<?php

namespace App\Http\Middleware\Addiction;

use Closure;
use Illuminate\Http\Request;

class RelapseUUID
{
    /**
     * Verifies that any addiction relapse UUIDs in the URL
     * string belong to the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if UUID is being passed in url string
        if(!is_null($addiction_relapse = $request->route('addiction_relapse')))
        {
            // Verify milestone belongs to user
            $addiction_ids = $request->user()->addictions->pluck('id')->toArray();
            if(!in_array($addiction_relapse->addiction_id, $addiction_ids))
            {
                return abort(403); // Return forbidden if different user's relapse
            }
        }
        return $next($request);
    }
}
