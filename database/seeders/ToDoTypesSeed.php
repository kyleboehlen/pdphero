<?php

namespace Database\Seeders;

// Models
use App\Models\ToDoTypes;

class ToDoTypesSeed extends AbstractSeeder
{
    const CONFIG = 'todo.types';
    const MODEL = ToDoTypes::class;

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
        Log::error("Failed to seed ToDo type: $id ($name)");
    }
}
