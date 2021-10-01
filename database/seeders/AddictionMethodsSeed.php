<?php

namespace Database\Seeders;

// Models
use App\Models\Addictions\AddictionMethod;

class AddictionMethodsSeed extends AbstractSeeder
{
    const CONFIG = 'addictions.methods';
    const MODEL = AddictionMethod::class;

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
        Log::error("Failed to seed Addiction Method: $id ($name)");
    }
}
