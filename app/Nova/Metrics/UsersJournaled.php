<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Carbon\Carbon;

// Models
use App\Models\Journal\JournalEntry;

class UsersJournaled extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $result = JournalEntry::select('user_id');
        $previous = JournalEntry::select('user_id');

        if($request->range != 'ALL')
        {
            $carbon = Carbon::now();
	        $carbon->setTimezone($request->user()->timezone);
            $carbon->subDays($request->range);
            $result = $result->where('created_at', '>=', $carbon->toDateTimeString());
            $previous = $previous->whereBetween('created_at', [
                (clone $carbon)->subDays($request->range)->toDateTimeString(),
                $carbon->toDateTimeString()
            ]);
        }

        $result = $result->groupBy('user_id')->get()->count();
        $previous = $previous->groupBy('user_id')->get()->count();

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
            1 => __('24 Hours'),
            30 => __('1 Month'),
            60 => __('60 Days'),
            365 => __('1 Year'),
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
        return 'users-journaled';
    }
}
