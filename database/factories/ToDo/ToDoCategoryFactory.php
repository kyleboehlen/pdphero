<?php

namespace Database\Factories\ToDo;

use App\Models\ToDo\ToDoCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

// Models
use App\Models\User\User;

class ToDoCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ToDoCategory::class;

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
