<?php

namespace Database\Factories\Addictions;

use App\Models\Addictions\AddictionRelapse;
use Illuminate\Database\Eloquent\Factories\Factory;

// Constants
use App\Helpers\Constants\Addiction\RelapseType;

class AddictionRelapseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AddictionRelapse::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type_id' => RelapseType::FULL_RELAPSE,
            'notes' => $this->faker->paragraph(),
        ];
    }
}
