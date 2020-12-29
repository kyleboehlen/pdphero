<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// Models
use App\Models\Habits\Habits;
use App\Models\User\User;

class HabitsTest extends TestCase
{
    /**
     * Tests habits UUID routes with fake UUIDs for 404 errors
     *
     * @return void
     * @test
     */
    public function testFakeUUIDs()
    {
        // Create test user
        $user = User::factory()->create();

        // Generate fake habits for user
        Habits::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Generate a todo and grab the UUID for testing
        $fake_habit = Habits::factory()->create();
        $uuid = $fake_habit->uuid;
        $this->assertTrue($fake_habit->delete());

        // Test edit route
        $response = $this->actingAs($user)->get(route('habits.edit', ['habit' => $uuid]));
        $response->assertStatus(404);

        // Test update route
        $response = $this->actingAs($user)->post(route('habits.update', ['habit' => $uuid]));
        $response->assertStatus(404);

        // Test destroy route
        $response = $this->actingAs($user)->post(route('habits.destroy', ['habit' => $uuid]));
        $response->assertStatus(404);

        // Test history route
        $response = $this->actingAs($user)->post(route('habits.history', ['habit' => $uuid]));
        $response->assertStatus(404);
    }

    /**
     * Tests habits UUID routes with a UUID that doesn't belong
     * to that user for 403 errors
     *
     * @return void
     * @test
     */
    public function testForbiddenUUIDs()
    {
        // Create test users
        $forbidden_user = User::factory()->create();
        $test_user = User::factory()->create();

        // Generate fake habits for user
        Habits::factory(rand(5, 15))->create([
            'user_id' => $forbidden_user->id,
        ]);

        // Get a forbidden UUID
        $uuid = Habits::where('user_id', $forbidden_user->id)->first()->uuid;

        // Test edit route
        $response = $this->actingAs($test_user)->get(route('habits.edit', ['habit' => $uuid]));
        $response->assertStatus(403);

        // Test update route
        $response = $this->actingAs($test_user)->post(route('habits.update', ['habit' => $uuid]));
        $response->assertStatus(403);

        // Test destroy route
        $response = $this->actingAs($test_user)->post(route('habits.destroy', ['habit' => $uuid]));
        $response->assertStatus(403);

        // Test history route
        $response = $this->actingAs($test_user)->post(route('habits.history', ['habit' => $uuid]));
        $response->assertStatus(403);
    }
}
