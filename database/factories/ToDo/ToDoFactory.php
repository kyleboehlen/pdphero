<?php

namespace Database\Factories\ToDo;

use App\Models\ToDo\ToDo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

// Costants
use App\Helpers\Constants\ToDo\Type;

// Models
use App\Models\ToDo\ToDoReminder;
use App\Models\User\User;

class ToDoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ToDo::class;

    public function configure()
    {
        return $this->afterCreating(function (ToDo $to_do){
            // Create reminders for the to do item
            $num_reminders = rand(0, 2);
            for($i = 0; $i < $num_reminders; $i++)
            {
                $carbon = Carbon::now()->addDays(rand(2, 10))->addHours(rand(2, 14))->addMinutes(rand(2, 40));
                $to_do_reminder = new ToDoReminder([
                    'to_do_id' => $to_do->id,
                    'remind_at' => $carbon->toDatetimeString(),
                ]);
                $to_do_reminder->save();
            }
        });
    }

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
