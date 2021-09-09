<?php

namespace App\Http\Middleware\Bucketlist;

use Closure;
use Illuminate\Http\Request;

class UUID
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if UUID is being passed in url string
        if(!is_null($bucketlist_item = $request->route('bucketlist_item')))
        {
            if($bucketlist_item->user_id != $request->user()->id) // Verify goal belongs to user
            {
                return abort(403); // Return forbidden if different user's goal
            }
        }
        return $next($request);
    }
}
