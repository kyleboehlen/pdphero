<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed ToDoPriority
        $this->call(ToDoPrioritySeed::class);

        // Seed ToDoTypes
        $this->call(ToDoTypesSeed::class);

        // Seed Settings
        $this->call(SettingsSeed::class);

        // Seed Habit Types
        $this->call(HabitTypesSeed::class);

        // Seed Habit History Types
        $this->call(HabitHistoryTypesSeed::class);

        // Seed Goal Types
        $this->call(GoalTypesSeed::class);

        // Seed Goal Statuses
        $this->call(GoalStatusesSeed::class);

        // Seed Goal Ad Hoc Periods
        $this->call(GoalAdHocPeriodsSeed::class);

        // Seed Home icons or whatever
        $this->call(HomeSeed::class);

        // Journal Moods
        $this->call(JournalMoodsSeed::class);
    }
}
