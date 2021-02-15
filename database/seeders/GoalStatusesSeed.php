<?php

namespace Database\Seeders;

// Models
use App\Models\Goal\GoalStatus;

class GoalStatusesSeed extends AbstractSeeder
{
    const CONFIG = 'goals.statuses';
    const MODEL = GoalStatus::class;

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
        Log::error("Failed to seed Goal Status: $id ($name)");
    }
}
