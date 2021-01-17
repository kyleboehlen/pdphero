<?php

namespace Tests\Deploy;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Models
use App\Models\Affirmations\Affirmations;
use App\Models\Habits\Habits;
use App\Models\ToDo\ToDo;
use App\Models\User\User;

class TestUserTest extends TestCase
{
    /**
     * Create a test user
     *
     * @test
     */
    public function userTest()
    {
        $user = User::factory()->create([
            'email' => 'test@test.com',
        ]);

        $this->assertNotNull(User::find($user->id));

        return $user;
    }

    /**
     * Give test user ToDo items
     * 
     * @depends userTest
     * @test
     */
    public function toDoTest(User $user)
    {
        ToDo::factory(rand(10, 30))->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(ToDo::where('user_id', $user->id)->get()->count() >= 5);
    }

    /**
     * Give test user Affirmations
     * 
     * @depends userTest
     * @test
     */
    public function affirmationsTest(User $user)
    {
        Affirmations::factory(rand(3, 7))->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(Affirmations::where('user_id', $user->id)->get()->count() >= 3);
    }

    /**
     * Give test user Habits
     * 
     * @depends userTest
     * @test
     */
    public function habitsTest(User $user)
    {
        Habits::factory(rand(3, 7))->create([
            'user_id' => $user->id,
        ]);

        $habits = Habits::where('user_id', $user->id)->get();

        $this->assertTrue($habits->count() >= 3);

        // Populate some values for each habit
        $test = $this;
        $habits->each(function($habit) use ($test){
            // Generate fake history
            $test->assertTrue($habit->generateFakeHistory());

            // Calculate strength based on fake
            $test->assertTrue($habit->calculateStrength());
        });
    }
}
