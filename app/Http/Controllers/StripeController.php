<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $user->createOrGetStripeCustomer();
        
        if(!$user->subscribed(config('membership.basic.slug')) && !$user->subscribed(config('membership.black_label.slug')))
        {
            // Check trial days left
            $trial_days_left = $user->getTrialDaysLeft();

            // Create checkouts
            $redirects = [
                'success_url' => route('profile'),
                'cancel_url' => route('stripe'),
            ];

            // Build checkouts
            if($trial_days_left >= 2)
            {
                $basic_checkout =
                    $user->newSubscription(
                            config('membership.basic.slug'),
                            config('membership.basic.stripe_price_id'))
                        ->trialDays($trial_days_left)
                        ->checkout($redirects);

                $black_label_checkout =
                    $user->newSubscription(
                            config('membership.black_label.slug'),
                            config('membership.black_label.stripe_price_id'))
                        ->trialDays($trial_days_left)
                        ->checkout($redirects);
            }
            else
            {
                $basic_checkout =
                    $user->newSubscription(
                            config('membership.basic.slug'),
                            config('membership.basic.stripe_price_id'))
                        ->checkout($redirects);

                $black_label_checkout =
                    $user->newSubscription(
                            config('membership.black_label.slug'),
                            config('membership.black_label.stripe_price_id'))
                        ->checkout($redirects);
            }

            // Return subscription view
            return view('stripe.subscription-selector')->with([
                'trial_days_left' => $trial_days_left,
                'basic_checkout' => $basic_checkout,
                'black_label_checkout' => $black_label_checkout,
                'stylesheet' => 'stripe',
            ]);
        }

        // Return billing portal
        return $user->redirectToBillingPortal(route('profile'));
    }
}
