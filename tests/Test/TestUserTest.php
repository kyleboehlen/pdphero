<?php

namespace Tests\Deploy;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Models
use App\Models\ToDo;
use App\Models\User;

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
        ToDo::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(ToDo::where('user_id', $user->id)->get()->count() >= 5);
    }
}
