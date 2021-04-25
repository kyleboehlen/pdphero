<?php

namespace Database\Seeders;

// Models
use App\Models\Goal\GoalTimePeriod;

class GoalTimePeriodsSeed extends AbstractSeeder
{
    const CONFIG = 'goals.time_periods';
    const MODEL = GoalTimePeriod::class;

    /**
     * Handles seed failures
     *
     * @return void
     */
    public function failure($type)
    {
        // Log Error
        $id = $type['id'];
        $name = $type['name'];
        Log::error("Failed to seed Goal Time Period: $id ($name)");
    }
}
