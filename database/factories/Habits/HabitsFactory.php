<?php

namespace Database\Factories\Habits;

use App\Models\Habits\Habits;
use Illuminate\Database\Eloquent\Factories\Factory;

// Constants
use App\Helpers\Constants\Habits\Type;

// Models
use App\Models\User\User;

class HabitsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Habits::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $days_of_week = null;
        $every_x_days = null;

        if(array_rand([true, false]))
        {
            $days_of_week = array();
            for($day = 0; $day <= 6; $day++) // For PHP datetime format 'w'
            {
                if(array_rand([true, false]))
                {
                    array_push($days_of_week, $day);
                }
            }
        }
        else
        {
            $every_x_days = rand(1, 10);
        }

        return [
            'name' => $this->faker->words(rand(3, 5), true),
            'user_id' => User::inRandomOrder()->first()->id,
            'type_id' => Type::USER_GENERATED,
            'notes' => (rand() % 2 == 0 ? $this->faker->paragraph() : null),
            'times_daily' => rand(1, 3),
            'days_of_week' => $days_of_week,
            'every_x_days' => $every_x_days,
            'show_todo' => (bool) (rand() % 2 == 0),
        ];
    }
}
