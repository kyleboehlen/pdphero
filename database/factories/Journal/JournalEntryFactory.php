<?php

namespace Database\Factories\Journal;

use App\Models\Journal\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

// Models
use App\Models\User\User;

class JournalEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JournalEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Generate day
        $days_back = rand(0, 365);
        $updated_at = Carbon::now()->subDays($days_back)->format('Y-m-d');

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'mood_id' => array_rand(config('journal.moods')),
            'title' => $this->faker->words(rand(3, 5), true),
            'updated_at' => $updated_at,
            'body' => array_rand([true, false]) ? $this->faker->paragraph() : null,
        ];
    }
}
