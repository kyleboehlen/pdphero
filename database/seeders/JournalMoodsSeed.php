<?php

namespace Database\Seeders;

// Models
use App\Models\Journal\JournalMood;

class JournalMoodsSeed extends AbstractSeeder
{
    const CONFIG = 'journal.moods';
    const MODEL = JournalMood::class;

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
        Log::error("Failed to seed Journal Mood: $id ($name)");
    }
}
