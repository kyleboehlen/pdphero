<?php

namespace Database\Factories\User;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'nutshell' => $this->faker->paragraph(rand(1, 3)) . "\r\n" . $this->faker->paragraph(rand(1, 6)) . "\r\n" . $this->faker->paragraph(rand(1, 3)) . "\r\n",
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'profile_picture' => 'test/' . rand(1, 7) . '.jpg',
            'values' => array_slice(config('test.values'), 0, rand(2, 6)),
        ];
    }
}
