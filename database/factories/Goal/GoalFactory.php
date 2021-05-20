<?php

namespace Database\Factories\Goal;

use App\Models\Goal\Goal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Goal\Type;

// Models
use App\Models\User\User;
use App\Models\Goal\GoalActionItem;
use App\Models\Goal\GoalCategory;
use App\Models\Habits\Habits;

class GoalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Goal::class;

    public function configure()
    {
        return $this->afterCreating(function (Goal $goal){
            switch($goal->type_id)
            {
                case Type::PARENT_GOAL:
                    // Create sub goals
                    $rand = rand(1, 5);
                    for($i = 0; $i < $rand; $i++)
                    {
                        $goal_type_id = array_rand(config('goals.types'));
                        $category = GoalCategory::where('user_id', $goal->user_id)->inRandomOrder()->first();
                        if(is_null($category))
                        {
                            $category = GoalCategory::factory()->create([
                                'user_id' => $goal->user_id,
                            ]);
                        }
                        $attr = [
                            'user_id' => $goal->user_id,
                            'parent_id' => $goal->id,
                            'category_id' => $category->id,
                        ];
                        switch($goal_type_id)
                        {
                            case Type::PARENT_GOAL:
                                Goal::factory()->parent()->create($attr);
                                break;
            
                            case Type::ACTION_AD_HOC:
                                Goal::factory()->adHoc()->create($attr);
                                break;
                            
                            case Type::ACTION_DETAILED:
                                Goal::factory()->actionPlan()->create($attr);
                                break;
            
                            case Type::HABIT_BASED:
                                $habit = Habits::where('user_id', $goal->user_id)->inRandomOrder()->first();
                                if(!is_null($habit))
                                {
                                    $attr['habit_id'] = $habit->id;
                                    Goal::factory()->habit()->create($attr);
                                }
                                break;
                            
                            case Type::FUTURE_GOAL:
                                Goal::factory()->future()->create($attr);
                                break;
            
                            case Type::MANUAL_GOAL:
                                Goal::factory()->manual()->create($attr);
                                break;
                        }
                    }
                    break;
                case Type::ACTION_AD_HOC:
                    // Create action items so with null deadlines and some not
                    $carbon = Carbon::parse($goal->start_date);
                    $carbon_end = Carbon::parse($goal->end_date);
            
                    while($carbon->lessThan($carbon_end))
                    {
                        $carbon->addDays(rand(5, 12));
                        GoalActionItem::factory()->create([
                            'goal_id' => $goal->id,
                            'deadline' => array_rand([true, false]) ? $carbon->format('Y-m-d') : null,
                        ]);
                    }
                    break;

                case Type::ACTION_DETAILED:
                    // Create action items so with null deadlines and some not
                    $carbon = Carbon::parse($goal->start_date);
                    $carbon_end = Carbon::parse($goal->end_date);
            
                    while($carbon->lessThan($carbon_end))
                    {
                        $carbon->addDays(rand(5, 12));
                        GoalActionItem::factory()->create([
                            'goal_id' => $goal->id,
                            'deadline' => $carbon->format('Y-m-d'),
                        ]);
                    }
                    break;
            }
        });
    }

    // STATES
    public function parent()
    {
        return $this->state(function (array $attributes) {
            return [
                'type_id' => Type::PARENT_GOAL,
                'name' => $this->faker->word() . ' Parent',
                'start_date' => \Carbon\Carbon::now()->subDays(rand(0, 100))->format('Y-m-d'),
                'end_date' => \Carbon\Carbon::now()->addDays(rand(0, 100))->format('Y-m-d'),
            ];
        });
    }

    public function adHoc()
    {
        return $this->state(function (array $attributes) {
            return [
                'type_id' => Type::ACTION_AD_HOC,
                'name' => $this->faker->word() . ' Ad Hoc',
                'start_date' => \Carbon\Carbon::now()->subDays(rand(0, 100))->format('Y-m-d'),
                'end_date' => \Carbon\Carbon::now()->addDays(rand(0, 100))->format('Y-m-d'),
                'custom_times' => rand(1, 4),
                'time_period_id' => array_rand(config('goals.time_periods')),
            ];
        });
    }

    public function actionPlan()
    {
        return $this->state(function (array $attributes) {
            return [
                'type_id' => Type::ACTION_DETAILED,
                'name' => $this->faker->word() . ' Action Plan',
                'start_date' => \Carbon\Carbon::now()->subDays(rand(0, 100))->format('Y-m-d'),
                'end_date' => \Carbon\Carbon::now()->addDays(rand(0, 100))->format('Y-m-d'),
            ];
        });
    }

    public function habit()
    {
        return $this->state(function (array $attributes) {
            return [
                'type_id' => Type::ACTION_DETAILED,
                'name' => $this->faker->word() . ' Habit',
                'start_date' => \Carbon\Carbon::now()->subDays(rand(0, 100))->format('Y-m-d'),
                'end_date' => \Carbon\Carbon::now()->addDays(rand(0, 100))->format('Y-m-d'),
                'habit_strength' => rand(1, 100),
            ];
        });
    }

    public function future()
    {
        return $this->state(function (array $attributes) {
            return [
                'type_id' => Type::FUTURE_GOAL,
                'name' => $this->faker->word() . ' Future',
                'achieved' => false,
            ];
        });
    }

    public function manual()
    {
        return $this->state(function (array $attributes) {
            return [
                'type_id' => Type::MANUAL_GOAL,
                'name' => $this->faker->word() . ' Manual',
                'start_date' => \Carbon\Carbon::now()->subDays(rand(0, 100))->format('Y-m-d'),
                'end_date' => \Carbon\Carbon::now()->addDays(rand(0, 100))->format('Y-m-d'),
                'custom_times' => rand(10, 30),
                'manual_completed' => rand(1, 35),
            ];
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Get user and category
        $user_id = User::inRandomOrder()->first()->id;
        $category = GoalCategory::where('user_id', $user_id)->inRandomOrder()->first();

        // Set notes
        if(array_rand([true, false]))
        {
            $notes = $this->faker->paragraph();
        }
        else
        {
            $notes = null;
        }

        // Return default state
        return [
            'user_id' => $user_id,
            'name' => $this->faker->words(rand(3, 8), true),
            'type_id' => array_rand(config('goals.types')),
            'status_id' => array_rand(config('goals.statuses')),
            'achieved' => array_rand([true, false]),
            'use_custom_img' => false,
            'progress' => rand(0, 100),
            'category_id' => (!is_null($category) && array_rand([true, false])) ? $category->id : null,
            'reason' => $this->faker->paragraph(),
            'notes' => $notes,
        ];
    }
}
