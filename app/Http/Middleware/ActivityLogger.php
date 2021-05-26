<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

// Models
use App\Models\User\Activity;

class ActivityLogger
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
            $route_name =  $request->route()->getName();

            // Log activity
            if(!is_null($route_name) && strpos($route_name, 'generated') === false && strpos($route_name, 'nova') === false)
            {
                Activity::create([
                    'user_id' => $user->id,
                    'route' => $route_name,
                ]);
            }
        }

        return $response;
    }
}
