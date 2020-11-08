<?php

namespace Database\Factories;

use App\Models\ToDo;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'type_id' => array_rand(config('todo.types')),
            'notes' => (rand() % 2 == 0 ? $this->faker->paragraph() : null),
            'completed' => (bool) rand() % 2 == 0,
        ];
    }
}
