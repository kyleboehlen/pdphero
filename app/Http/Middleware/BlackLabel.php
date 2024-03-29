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

        // if(!is_null($request->user()) && config('app.env') != 'local')
        // {
        //     $user = $request->user();

        //     // Check if they're black label
        //     if(!$user->subscribed(config('membership.black_label.slug')))
        //     {
        //         // Check if user is basic
        //         if($user->subscribed(config('membership.basic.slug')))
        //         {
		//             // Check if user has upgraded
        //             if($user->subscription(config('membership.basic.slug'))->stripe_plan == config('membership.black_label.stripe_price_id'))
        //             {
        //                 $user->subscription(config('membership.basic.slug'))->update(['name' => config('membership.black_label.slug')]);
        //                 return redirect()->route($request->route()->getName());
        //             }

        //             return redirect()->back()->withErrors(['black-label-upgrade' => $user->billingPortalUrl(route($request->route()->getName()))]);
        //         }
        //         elseif($user->getTrialDaysLeft() > 0) // Check free trial period
        //         {
        //             return redirect()->route('stripe')->withErrors(['info' => 'This feature requires a Black Label subscription, you will retain any days left in your free trial if you subscribe.']);
        //         }
        //         else
        //         {
        //             return redirect()->route('stripe');
        //         }
        //     }
        //     else
        //     {
        //         // Check if user has downgraded
        //         if($user->subscription(config('membership.black_label.slug'))->stripe_plan == config('membership.basic.stripe_price_id'))
        //         {
        //             $user->subscription(config('membership.black_label.slug'))->update(['name' => config('membership.basic.slug')]);
        //             return redirect()->route($request->route()->getName());
        //         }
        //     }
        // }

        return $response;
    }
}
