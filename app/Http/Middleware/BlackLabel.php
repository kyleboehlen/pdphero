<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlackLabel
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
            $user->createOrGetStripeCustomer();

            // Check if they're black label
            if(!$user->subscribed(config('membership.black_label.slug')))
            {
                // Check free trial period
                if($user->getTrialDaysLeft() > 0)
                {
                    return redirect()->route('stripe')->withErrors(['info' => 'This feature requires a Black Label subscription, you will retain any days left in your free trial if you subscribe.']);
                }
                elseif($user->subscribed(config('membership.basic.slug')))
                {
                    return redirect()->back()->withErrors(['black-label-upgrade' => $user->billingPortalUrl(route($request->route()->getName()))]);
                }
                else
                {
                    return redirect()->route('stripe');
                }
            }
        }

        return $response;
    }
}
