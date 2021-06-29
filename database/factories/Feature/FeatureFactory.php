<?php

namespace Database\Factories\Feature;

use App\Models\Feature\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;

// Models
use App\Models\Feature\FeatureVote;
use App\Models\User\User;

class FeatureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Feature::class;

    public function configure()
    {
        return $this->afterCreating(function (Feature $feature){
            $users = User::all();

            foreach($users as $user)
            {
                // Randomly determine whether or not to create a vote
                if(rand() % 2 == 0)
                {
                    $vote = new FeatureVote([
                        'feature_id' => $feature->id,
                        'user_id' => $user->id,
                    ]);

                    $vote->value = (1 - rand(0, 2));
                    $vote->save();
                }
            }

            $feature->calculateScore();
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
            'name' => $this->faker->words(rand(3, 8), true),
            'desc' => $this->faker->paragraph(),
        ];
    }
}
