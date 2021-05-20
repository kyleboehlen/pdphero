<?php

namespace Database\Seeders;

// Models
use App\Models\Goal\GoalType;

class GoalTypesSeed extends AbstractSeeder
{
    const CONFIG = 'goals.types';
    const MODEL = GoalType::class;

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
        Log::error("Failed to seed Goal Type: $id ($name)");
    }
}
