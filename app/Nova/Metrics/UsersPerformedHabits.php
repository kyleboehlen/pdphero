<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Habits\HistoryType;

// Models
use App\Models\Habits\Habits;
use App\Models\Habits\HabitHistory;

class UsersPerformedHabits extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $result = HabitHistory::select('habit_id')->where('type_id', HistoryType::COMPLETED);
        $previous = HabitHistory::select('habit_id')->where('type_id', HistoryType::COMPLETED);

        if($request->range != 'ALL')
        {
            $carbon = Carbon::now();
            $carbon->subDays($request->range);
            $result = $result->where('day', '>=', $carbon->toDateTimeString());
            $previous = $previous->whereBetween('day', [
                (clone $carbon)->subDays($request->range)->toDateTimeString(),
                $carbon->toDateTimeString()
            ]);
        }

        $result = Habits::select('user_id')->whereIn('id', $result->groupBy('habit_id')->get()->pluck('habit_id')->toArray())->groupBy('user_id')->count();
        $previous = Habits::select('user_id')->whereIn('id', $previous->groupBy('habit_id')->get()->pluck('habit_id')->toArray())->groupBy('user_id')->count();

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
        return 'users-performed-habits';
    }
}
