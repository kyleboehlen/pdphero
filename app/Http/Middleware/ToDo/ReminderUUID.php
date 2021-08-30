<?php

namespace App\Http\Middleware\ToDo;

use Closure;
use Illuminate\Http\Request;

class ReminderUUID
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
        if(!is_null($reminder = $request->route('reminder')))
        {
            $reminder->load('todo');
            if($reminder->todo->user_id != $request->user()->id) // Verify reminder belongs to user
            {
                return abort(403); // Return forbidden if different user's reminder
            }
        }
        return $next($request);
    }
}
