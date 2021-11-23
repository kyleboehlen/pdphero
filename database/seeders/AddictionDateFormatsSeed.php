<?php

namespace Database\Seeders;

// Models
use App\Models\Addictions\AddictionDateFormat;

class AddictionDateFormatsSeed extends AbstractSeeder
{
    const CONFIG = 'addictions.date_formats';
    const MODEL = AddictionDateFormat::class;

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
        Log::error("Failed to seed Addiction Date Format: $id ($name)");
    }
}
