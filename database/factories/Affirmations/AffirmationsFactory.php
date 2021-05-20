<?php

namespace Database\Factories\Affirmations;

use App\Models\Affirmations\Affirmations;
use Illuminate\Database\Eloquent\Factories\Factory;

// Models
use App\Models\User\User;

class AffirmationsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Affirmations::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'value' => config('test.affirmations')[array_rand(config('test.affirmations'))],
        ];
    }
}
