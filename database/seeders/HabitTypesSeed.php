<?php

namespace Database\Seeders;

// Models
use App\Models\Habits\HabitTypes;

class HabitTypesSeed extends AbstractSeeder
{
    const CONFIG = 'habits.types';
    const MODEL = HabitTypes::class;

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
        Log::error("Failed to seed Habit type: $id ($name)");
    }
}
