<?php

namespace Database\Seeders;

// Models
use App\Models\User\Settings;

class SettingsSeed extends AbstractSeeder
{
    const CONFIG = 'settings.seed';
    const MODEL = Settings::class;

    /**
     * Handles seed failures
     *
     * @return void
     */
    public function failure($setting)
    {
        // Log Error
        $id = $setting['id'];
        $desc = $setting['desc'];
        Log::error("Failed to seed Setting: $id ($desc)");
    }
}
