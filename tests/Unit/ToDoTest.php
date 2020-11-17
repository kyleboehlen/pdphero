<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// Models
use App\Models\User;
use App\Models\ToDo\ToDo;

class ToDoTest extends TestCase
{
    /**
     * Tests todo UUID routes with fake UUIDs for 404 errors
     *
     * @return void
     * @test
     */
    public function testFakeUUIDs()
    {
        // Create test user
        $user = User::factory()->create();

        // Generate fake ToDos for user
        ToDo::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Generate a todo and grab the UUID for testing
        $fake_todo = ToDo::factory()->create();
        $uuid = $fake_todo->uuid;
        $this->assertTrue($fake_todo->delete());

        // Test edit route
        $response = $this->actingAs($user)->get(route('todo.edit', ['todo' => $uuid]));
        $response->assertStatus(404);

        // Test update route
        $response = $this->actingAs($user)->post(route('todo.update', ['todo' => $uuid]));
        $response->assertStatus(404);

        // Test destroy route
        $response = $this->actingAs($user)->post(route('todo.destroy', ['todo' => $uuid]));
        $response->assertStatus(404);

        // Test toggle completed route
        $response = $this->actingAs($user)->post(route('todo.toggle-completed', ['todo' => $uuid]));
        $response->assertStatus(404);
    }

    /**
     * Tests todo UUID routes with a UUID that doesn't belong
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

        // Generate fake ToDos for user
        ToDo::factory(rand(5, 15))->create([
            'user_id' => $forbidden_user->id,
        ]);

        // Get a forbidden UUID
        $uuid = ToDo::where('user_id', $forbidden_user->id)->first()->uuid;

        // Test edit route
        $response = $this->actingAs($test_user)->get(route('todo.edit', ['todo' => $uuid]));
        $response->assertStatus(403);

        // Test update route
        $response = $this->actingAs($test_user)->post(route('todo.update', ['todo' => $uuid]));
        $response->assertStatus(403);

        // Test destroy route
        $response = $this->actingAs($test_user)->post(route('todo.destroy', ['todo' => $uuid]));
        $response->assertStatus(403);

        // Test toggle completed route
        $response = $this->actingAs($test_user)->post(route('todo.toggle-completed', ['todo' => $uuid]));
        $response->assertStatus(403);
    }    
}
