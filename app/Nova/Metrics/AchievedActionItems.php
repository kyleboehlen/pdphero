<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Carbon\Carbon;

// Models
use App\Models\Goal\GoalActionItem;

class AchievedActionItems extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $result = GoalActionItem::where('achieved', 1);
        $previous = GoalActionItem::where('achieved', 1);

        if($request->range != 'ALL')
        {
            $carbon = Carbon::now();
            $carbon->subDays($request->range);
            $result = $result->where('updated_at', '>=', $carbon->toDateTimeString());
            $previous = $previous->whereBetween('updated_at', [
                (clone $carbon)->subDays($request->range)->toDateTimeString(),
                $carbon->toDateTimeString()
            ]);
        }

        $result = $result->get()->count();
        $previous = $previous->get()->count();

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
            30 => __('1 Month'),
            60 => __('60 Days'),
            365 => __('1 Year'),
            1 => __('24 Hours'),
            'ALL' => __('All Time'),
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
        return 'achieved-action-items';
    }
}
