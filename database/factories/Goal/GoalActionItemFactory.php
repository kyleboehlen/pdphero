<?php

namespace Database\Factories\Goal;

use App\Models\Goal\GoalActionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

// Models
use App\Models\Goal\Goal;

class GoalActionItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GoalActionItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'goal_id' => Goal::inRandomOrder()->first()->id,
            'name' => $this->faker->words(rand(3, 8), true),
        ];
    }
}
