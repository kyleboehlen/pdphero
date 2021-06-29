<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Membership
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
        $response = $next($request);

        if(!is_null($request->user()))
        {
            $user = $request->user();

            // Check free trial period
            if($user->getTrialDaysLeft() == 0)
            {
                // Check membership status
                if(!$user->subscribed(config('membership.basic.slug')) && !$user->subscribed(config('membership.black_label.slug')))
                {
                    return redirect()->route('stripe');
                }
            }
        }

        return $response;
    }
}
