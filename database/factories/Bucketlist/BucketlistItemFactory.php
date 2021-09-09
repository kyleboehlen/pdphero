<?php

namespace Database\Factories\Bucketlist;

use App\Models\Bucketlist\BucketlistItem;
use Illuminate\Database\Eloquent\Factories\Factory;

// Models
use App\Models\User\User;

class BucketlistItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BucketlistItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(rand(3, 8), true),
            'user_id' => User::inRandomOrder()->first()->id,
            'details' => (rand() % 2 == 0 ? $this->faker->paragraph() : null),
            'completed' => (bool) rand(0, 1),
        ];
    }
}
