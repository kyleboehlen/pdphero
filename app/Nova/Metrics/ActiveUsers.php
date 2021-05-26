<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Carbon\Carbon;

// Models
use App\Models\User\Activity;

class ActiveUsers extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $carbon = Carbon::now();
        $carbon->subDays($request->range);
        $result = Activity::select('user_id')->where('created_at', '>=', $carbon->toDateTimeString())->groupBy('user_id')->get()->count();
        $previous = Activity::select('user_id')->whereBetween('created_at', [(clone $carbon)->subDays($request->range)->toDateTimeString(), $carbon->toDateTimeString()])->groupBy('user_id')->get()->count();

        return $this->result($result)->previous($previous);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            1 => __('Today'),
            30 => __('30 Days'),
            60 => __('60 Days'),
            365 => __('365 Days'),
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'active-users';
    }
}
