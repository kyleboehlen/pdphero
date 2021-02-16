<?php

namespace Database\Factories\Goal;

use App\Models\Goal\Goal;
use Illuminate\Database\Eloquent\Factories\Factory;

// Models
use App\Models\User\User;

class GoalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Goal::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'name' => $this->faker->words(rand(3, 8), true),
            'type_id' => array_rand(config('goals.types')),
            'status_id' => array_rand(config('goals.statuses')),
            'achieved' => array_rand([true, false]),
            'use_custom_img' => false,
            'progress' => rand(0, 100),
        ];
    }
}
