<?php

namespace Database\Seeders;

// Models
use App\Models\Goal\GoalAdHocPeriod;

class GoalAdHocPeriodsSeed extends AbstractSeeder
{
    const CONFIG = 'goals.ad_hoc_periods';
    const MODEL = GoalAdHocPeriod::class;

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
        Log::error("Failed to seed Goal Ad Hoc Period: $id ($name)");
    }
}
