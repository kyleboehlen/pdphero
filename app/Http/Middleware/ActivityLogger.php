<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
            $user_id = $request->user()->id;
            $route_name =  $request->route()->getName();
            Activity::create([
                'user_id' => $user_id,
                'route' => $route_name,
            ]);
        }

        return $response;
    }
}
