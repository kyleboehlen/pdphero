<?php

namespace App\Http\Middleware\FirstVisit;

use Closure;
use Illuminate\Http\Request;
use Log;

// Models
use App\Models\FirstVisit\FirstVisitDisplayed;

class Messages
{
    /**
     * Finds any messages to flash
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verify user is authenticated
        $user = $request->user();

        // Check for any messages
        $first_visit = $user->firstVisitMessage($request->route()->getName());
        if(!is_null($first_visit))
        {
            // Flash first visit message
            $request->session()->now('first_visit_message', $first_visit->message);

            // Mark that the message was displayed to the user
            if(!FirstVisitDisplayed::create([
                'user_id' => $user->id,
                'message_id' => $first_visit->id,
            ]))
            {
                // Log error
                Log::error('Failed to mark a first visit message as displayed', [
                    'user_id' => $user->id,
                    'message_id' => $first_visit->id,
                ]);
            }
        }
        
        return $next($request);
    }
}
