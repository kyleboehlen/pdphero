<?php

namespace Database\Factories\Addictions;

use App\Models\Addictions\Addiction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Addiction\Method;
use App\Helpers\Constants\Addiction\RelapseType;

// Models
use App\Models\User\User;

class AddictionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Addiction::class;

    public function configure()
    {
        return $this->afterCreating(function (Addiction $addiction){
            // Set milestones
            buildDefaultMilestones($addiction);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $method_id = array_rand(config('addictions.methods'));
        if($method_id == Method::MODERATION)
        {
            $moderated_date_format = array_rand(config('addictions.date_formats'));
            $moderated_amount = rand(1, config('addictions.date_formats')[$moderated_date_format]['max']);
            $moderated_period = rand(1, 10);
        }
        else
        {
            $moderated_amount = null;
            $moderated_period = null;
            $moderated_date_format = null;
        }

        $start_date = Carbon::now()->subDays(rand(1, 450))->toDateString();

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'name' => $this->faker->words(rand(1, 3), true),
            'method_id' => $method_id,
            'details' => $this->faker->paragraph(),
            'start_date' => $start_date,
            'moderated_amount' => $moderated_amount,
            'moderated_period' => $moderated_period,
            'moderated_date_format' => $moderated_date_format,
        ];
    }
}
