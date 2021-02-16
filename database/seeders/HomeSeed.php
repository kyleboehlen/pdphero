<?php

namespace Database\Seeders;

// Models
use App\Models\Home\Home;

class HomeSeed extends AbstractSeeder
{
    const CONFIG = 'home';
    const MODEL = Home::class;

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
        Log::error("Failed to seed Home: $id ($name)");
    }
}
