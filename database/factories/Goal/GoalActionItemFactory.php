<?php

namespace Database\Factories\Goal;

use App\Models\Goal\GoalActionItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

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
            'achieved' => array_rand([true, false]),
            'deadline' => Carbon::now()->format('Y-m-d'),
        ];
    }
}
