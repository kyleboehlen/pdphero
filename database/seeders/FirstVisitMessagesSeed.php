<?php

namespace Database\Seeders;

// Models
use App\Models\FirstVisit\FirstVisitMessages;

class FirstVisitMessagesSeed extends AbstractSeeder
{
    const CONFIG = 'first-visit.messages';
    const MODEL = FirstVisitMessages::class;

    /**
     * Handles seed failures
     *
     * @return void
     */
    public function failure($type)
    {
        // Log Error
        $route = $type['route_name'];
        $message = $type['message'];
        Log::error("Failed to seed First Visit Message: $route ($message)");
    }
}
