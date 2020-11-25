<?php

namespace Database\Factories\ToDo;

use App\Models\ToDo\ToDo;
use Illuminate\Database\Eloquent\Factories\Factory;

// Costants
use App\Helpers\Constants\ToDo\Type;

// Models
use App\Models\User;

class ToDoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ToDo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->words(rand(3, 8), true),
            'user_id' => User::inRandomOrder()->first()->id,
            'priority_id' => array_rand(config('todo.priorities')),
            'type_id' => Type::TODO_ITEM,
            'notes' => (rand() % 2 == 0 ? $this->faker->paragraph() : null),
            'completed' => (bool) rand(0, 1),
        ];
    }
}
