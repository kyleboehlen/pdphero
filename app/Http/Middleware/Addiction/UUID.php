<?php

namespace App\Http\Middleware\Addiction;

use Closure;
use Illuminate\Http\Request;

class UUID
{
    /**
     * Verifies that any addiction UUIDs in the URL
     * string belong to the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if UUID is being passed in url string
        if(!is_null($addiction = $request->route('addiction')))
        {
            if($addiction->user_id != $request->user()->id) // Verify addiction belongs to user
            {
                return abort(403); // Return forbidden if different user's addiction
            }
        }
        return $next($request);
    }
}
