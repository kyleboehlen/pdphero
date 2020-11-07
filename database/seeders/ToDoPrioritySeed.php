<?php

namespace Database\Seeders;

// Models
use App\Models\ToDoPriority;

class ToDoPrioritySeed extends AbstractSeeder
{
    const CONFIG = 'todo.priorities';
    const MODEL = ToDoPriority::class;

    /**
     * Handles seed failures
     *
     * @return void
     */
    public function failure($priority)
    {
        // Log Error
        $id = $priority['id'];
        $name = $priority['name'];
        Log::error("Failed to seed ToDo priority: $id ($name)");
    }
}
