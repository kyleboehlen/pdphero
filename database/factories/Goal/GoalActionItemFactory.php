<?php

namespace Database\Factories\Goal;

use App\Models\Goal\GoalActionItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

// Models
use App\Models\Goal\Goal;
use App\Models\Goal\GoalActionItemReminder;

class GoalActionItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GoalActionItem::class;

    public function configure()
    {
        return $this->afterCreating(function (GoalActionItem $action_item){
            // Create reminders for the action item
            $num_reminders = rand(0, 2);
            for($i = 0; $i < $num_reminders; $i++)
            {
                $carbon = Carbon::now()->addDays(rand(2, 10))->addHours(rand(2, 14))->addMinutes(rand(2, 40));
                $action_item_reminder = new GoalActionItemReminder([
                    'action_item_id' => $action_item->id,
                    'remind_at' => $carbon->toDatetimeString(),
                ]);
                $action_item_reminder->save();
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
            'goal_id' => Goal::inRandomOrder()->first()->id,
            'name' => $this->faker->words(rand(3, 8), true),
            'achieved' => array_rand([true, false]),
            'deadline' => Carbon::now()->format('Y-m-d'),
        ];
    }
}
