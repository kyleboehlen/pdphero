<?php

namespace Database\Seeders;

// Models
use App\Models\Addictions\AddictionRelapseType;

class AddictionRelapseTypesSeed extends AbstractSeeder
{
    const CONFIG = 'addictions.relapse.types';
    const MODEL = AddictionRelapseType::class;

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
        Log::error("Failed to seed Addiction Relapse Type: $id ($name)");
    }
}
