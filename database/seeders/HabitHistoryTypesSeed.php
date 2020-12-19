<?php

namespace Database\Seeders;

// Models
use App\Models\Habits\HabitHistoryTypes;

class HabitHistoryTypesSeed extends AbstractSeeder
{
    const CONFIG = 'habits.history_types';
    const MODEL = HabitHistoryTypes::class;

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
        Log::error("Failed to seed Habit History type: $id ($name)");
    }
}
