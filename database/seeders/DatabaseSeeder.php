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
    }
}
