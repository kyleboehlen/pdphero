<?php

namespace Database\Factories\Goal;

use App\Models\Goal\GoalCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

// Models
use App\Models\User\User;

class GoalCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GoalCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'name' => $this->faker->words(rand(1, 3), true),
        ];
    }
}
