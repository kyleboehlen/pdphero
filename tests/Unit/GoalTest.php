<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Habits\HistoryType;

// Models
use App\Models\Goal\Goal;
use App\Models\Goal\GoalActionItem;
use App\Models\User\User;

class GoalTest extends TestCase
{
    /**
     * Tests goal UUID routes with fake UUIDs for 404 errors
     *
     * @return void
     * @test
     */
    public function testFakeUUIDs()
    {
        // Create test user
        $user = User::factory()->create();

        // Generate fake goals for user
        $goals = Goal::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Generate a goal and grab the UUID for testing
        $fake_goal = Goal::factory()->create();
        $goal_uuid = $fake_goal->uuid;
        $this->assertTrue($fake_goal->delete());

        // Generate fake action items for user
        $action_items = GoalActionItem::factory(rand(5, 15))->create([
            'goal_id' => $goals->random()->id,
        ]);


        // Generate a action item and grab the UUID for testing
        $fake_action_item = GoalActionItem::factory()->create();
        $action_item_uuid = $fake_action_item->uuid;
        $this->assertTrue($fake_action_item->delete());

        // Test toggle completed routes
        $response = $this->actingAs($user)->post(route('goals.toggle-completed.goal', ['goal' => $goal_uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->post(route('goals.toggle-completed.action-item', ['action_item' => $action_item_uuid]));
        $response->assertStatus(404);

        // Test view routes
        $response = $this->actingAs($user)->get(route('goals.view.goal', ['goal' => $goal_uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->get(route('goals.view.action-item', ['action_item' => $action_item_uuid]));
        $response->assertStatus(404);

        // Test edit routes
        $response = $this->actingAs($user)->get(route('goals.edit.goal', ['goal' => $goal_uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->get(route('goals.edit.action-item', ['action_item' => $action_item_uuid]));
        $response->assertStatus(404);

        // Test update routes
        $response = $this->actingAs($user)->post(route('goals.update.goal', ['goal' => $goal_uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->post(route('goals.update.action-item', ['action_item' => $action_item_uuid]));
        $response->assertStatus(404);

        // Test destroy routes
        $response = $this->actingAs($user)->post(route('goals.destroy.goal', ['goal' => $goal_uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->post(route('goals.destroy.action-item', ['action_item' => $action_item_uuid]));
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

        // Generate fake goals for user
        $goals = Goal::factory(rand(5, 15))->create([
            'user_id' => $forbidden_user->id,
        ]);

        // Get a forbidden UUID
        $goal = $goals->random();
        $goal_uuid = $goal->uuid;

        // Generate fake action items for user
        $action_items = GoalActionItem::factory(rand(5, 15))->create([
            'goal_id' => $goal->id,
        ]);

        // Get a forbidden UUID
        $action_item_uuid = $action_items->random()->uuid;

        // Test toggle completed routes
        $response = $this->actingAs($test_user)->post(route('goals.toggle-completed.goal', ['goal' => $goal_uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($test_user)->post(route('goals.toggle-completed.action-item', ['action_item' => $action_item_uuid]));
        $response->assertStatus(403);

        // Test view routes
        $response = $this->actingAs($test_user)->get(route('goals.view.goal', ['goal' => $goal_uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($test_user)->get(route('goals.view.action-item', ['action_item' => $action_item_uuid]));
        $response->assertStatus(403);

        // Test edit routes
        $response = $this->actingAs($test_user)->get(route('goals.edit.goal', ['goal' => $goal_uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($test_user)->get(route('goals.edit.action-item', ['action_item' => $action_item_uuid]));
        $response->assertStatus(403);

        // Test update routes
        $response = $this->actingAs($test_user)->post(route('goals.update.goal', ['goal' => $goal_uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($test_user)->post(route('goals.update.action-item', ['action_item' => $action_item_uuid]));
        $response->assertStatus(403);

        // Test destroy routes
        $response = $this->actingAs($test_user)->post(route('goals.destroy.goal', ['goal' => $goal_uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($test_user)->post(route('goals.destroy.action-item', ['action_item' => $action_item_uuid]));
        $response->assertStatus(403);
    }
}
