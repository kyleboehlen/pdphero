<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Goal\Type;

// Models
use App\Models\Goal\Goal;
use App\Models\Goal\GoalActionItem;
use App\Models\Goal\GoalCategory;
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
        $response = $this->actingAs($user)->post(route('goals.toggle-achieved.goal', ['goal' => $goal_uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->post(route('goals.toggle-achieved.action-item', ['action_item' => $action_item_uuid]));
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
        $response = $this->actingAs($test_user)->post(route('goals.toggle-achieved.goal', ['goal' => $goal_uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($test_user)->post(route('goals.toggle-achieved.action-item', ['action_item' => $action_item_uuid]));
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

    /**
     * Tests viewing goals works
     *
     * @return void
     * @test
     */
    public function testIndex()
    {
        // Create test user
        $user = User::factory()->create();

        // Create category
        $category = GoalCategory::factory()->create(['user_id' => $user->id]);

        // Generate test goals
        $attr = [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'parent_id' => null,
        ];
        Goal::factory()->parent()->create($attr);
        Goal::factory()->adHoc()->create($attr);
        Goal::factory()->actionPlan()->create($attr);
        Goal::factory()->future()->create($attr);
        Goal::factory()->manual()->create($attr);

        // Assert see index page
        $goals = Goal::where('user_id', $user->id)->whereNull('parent_id')->get();
        $response = $this->actingAs($user)->get(route('goals', ['scope' => 'all']));
        $response->assertOk();
        foreach($goals as $goal)
        {
            $response->assertSee($goal->name);
        }
    }

    /**
     * Tests viewing the goal types page works
     *
     * @return void
     * @test
     */
    public function testViewTypes()
    {
        // Create test user
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('goals.types'));
        $response->assertOk();
        foreach(config('goals.types') as $goal_type)
        {
            $response->assertSee($goal_type['name']);
        }
    }

    /**
     * Tests toggling the goal route works
     *
     * @return void
     * @test
     */
    public function testToggleGoal()
    {
        // Create test user
        $user = User::factory()->create();

        // Create goal
        $goal = Goal::factory()->manual()->create(['user_id' => $user->id, 'achieved' => false]);

        // Toggle goal
        $response = $this->actingAs($user)->post(route('goals.toggle-achieved.goal', ['goal' => $goal->uuid]));
        $response->assertRedirect("/goals/view/goal/$goal->uuid");

        // Check if it's toggled
        $goal->refresh();
        $this->assertTrue((bool) $goal->achieved);
    }

    /**
     * Tests toggling the action item route works
     *
     * @return void
     * @test
     */
    public function testToggleActionItem()
    {
        // Create test user
        $user = User::factory()->create();

        // Create goal
        $goal = Goal::factory()->adHoc()->create(['user_id' => $user->id]);

        // Create action item
        $action_item = GoalActionItem::factory()->create(['goal_id' => $goal->id, 'achieved' => false]);

        // Toggle action item
        $response = $this->actingAs($user)->post(route('goals.toggle-achieved.action-item', ['action_item' => $action_item->uuid, 'view_details' => true]));
        $response->assertRedirect("/goals/view/action-item/$action_item->uuid");

        // Check if it's toggled
        $action_item->refresh();
        $this->assertTrue((bool) $action_item->achieved);

        // Check if we get the proper redirect without details
        $response = $this->actingAs($user)->post(route('goals.toggle-achieved.action-item', ['action_item' => $action_item->uuid]));
        $response->assertRedirect("/goals/view/goal/$goal->uuid?selected-dropdown=action-plan");
    }

    /**
     * Tests viewing goal details works
     *
     * @return void
     * @test
     */
    public function testViewGoal()
    {
        // Create test user
        $user = User::factory()->create();

        // Create goal
        $goal = Goal::factory()->adHoc()->create(['user_id' => $user->id]);

        // See if we can view the details page
        $response = $this->actingAs($user)->get(route('goals.view.goal', ['goal' => $goal->uuid]));
        $response->assertOk();
        $response->assertSee($goal->name);
        $response->assertSee($goal->reason);
    }

    /**
     * Tests viewing action item details works
     *
     * @return void
     * @test
     */
    public function testViewActionItem()
    {
        // Create test user
        $user = User::factory()->create();

        // Create goal
        $goal = Goal::factory()->adHoc()->create(['user_id' => $user->id]);

        // Create action item
        $action_item = GoalActionItem::factory()->create(['goal_id' => $goal->id, 'notes' => 'My brain has fucking melted']);

        // Check if we can view the details pageeeeee -- thanks louie, def needed those extra e's
        $response = $this->actingAs($user)->get(route('goals.view.action-item', ['action_item' => $action_item->uuid]));
        $response->assertOk();
        $response->assertSee($action_item->name);
        $response->assertSee($action_item->notes);
    }

    /**
     * Tests creating a goal works
     *
     * @return void
     * @test
     */
    public function testCreateGoal()
    {
        // Create test user
        $user = User::factory()->create();

        // Check the select type create page
        $response = $this->actingAs($user)->get(route('goals.create.goal'));
        $response->assertOk();
        $response->assertSee('Select a Goal Type');

        // Now see if we can get the create form
        foreach(config('goals.types') as $key => $goal_type)
        {
            $response = $this->actingAs($user)->get(route('goals.create.goal', ['type' => $key]));
            $response->assertOk();
            $response->assertSee('Create New ' . $goal_type['name']);
        }

        // Test the store route
        $name = 'Whatever';
        $start_date = Carbon::now();
        $end_date = $start_date->addDay()->format('Y-m-d');
        $start_date = $start_date->subDay()->format('Y-m-d');
        $reason = 'Blah blah blah';
        $response = $this->actingAs($user)->followingRedirects()->post(route('goals.store.goal'), [
            '_token' => csrf_token(),
            'category' => 'no-category',
            'type' => 1,
            'name' => $name,
            'start-date' => $start_date,
            'end-date' => $end_date,
            'reason' => $reason,
        ]);

        $goal = Goal::where('user_id', $user->id)->first();
        $this->assertTrue(!is_null($goal));
        $this->assertEquals($goal->name, $name);
        $this->assertEquals($goal->start_date, $start_date);
        $this->assertEquals($goal->end_date, $end_date);
        $this->assertEquals($goal->reason, $reason);
    }

    /**
     * Tests creating an action item works
     *
     * @return void
     * @test
     */
    public function testCreateActionItem()
    {
        // Create test user
        $user = User::factory()->create();

        // Create goal we can put action items under
        $goal = Goal::factory()->adHoc()->create(['user_id' => $user->id]);
        GoalActionItem::where('goal_id', $goal->id)->delete();

        // See we get the action item form
        $response = $this->actingAs($user)->get(route('goals.create.action-item', ['goal' => $goal->uuid]));
        $response->assertOk();
        $response->assertSee("Create&nbsp;$goal->name Action Item", false);

        // Test the store route
        $name = 'Whatever';
        $notes = 'Blah blah blah';
        $response = $this->actingAs($user)->followingRedirects()->post(route('goals.store.action-item', ['goal' => $goal->uuid]), [
            '_token' => csrf_token(),
            'name' => $name,
            'notes' => $notes,
        ]);
        $action_item = GoalActionItem::where('goal_id', $goal->id)->first();
        $this->assertTrue(!is_null($action_item));
        $this->assertEquals($action_item->name, $name);
        $this->assertEquals($action_item->notes, $notes);
    }

    /**
     * Tests setting an action item deadline
     *
     * @return void
     * @test
     */
    public function testSetDeadline()
    {
        // Create test user
        $user = User::factory()->create();

        // Create goal
        $goal = Goal::factory()->adHoc()->create(['user_id' => $user->id]);

        // Create action item
        $action_item = GoalActionItem::factory()->create(['goal_id' => $goal->id, 'achieved' => false, 'deadline' => null]);

        // Set the deadline
        $deadline = Carbon::now()->format('Y-m-d');
        $response = $this->actingAs($user)->post(route('goals.ad-hoc-deadline.set', ['action_item' => $action_item->uuid, 'view_details' => true]), [
            'deadline' => $deadline,
        ]);
        $response->assertRedirect("/goals/view/action-item/$action_item->uuid");

        // Check if it's toggled
        $action_item->refresh();
        $this->assertEquals($action_item->deadline, $deadline);
    }

    /**
     * Tests clearing an action item deadline
     *
     * @return void
     * @test
     */
    public function testClearDeadline()
    {
        // Create test user
        $user = User::factory()->create();

        // Create goal
        $goal = Goal::factory()->adHoc()->create(['user_id' => $user->id]);

        // Create action item
        $deadline = Carbon::now()->format('Y-m-d');
        $action_item = GoalActionItem::factory()->create(['goal_id' => $goal->id, 'achieved' => false, 'deadline' => $deadline]);

        // Clear the deadline
        $response = $this->actingAs($user)->post(route('goals.ad-hoc-deadline.clear', ['action_item' => $action_item->uuid]));
        $response->assertRedirect("/goals/view/action-item/$action_item->uuid");

        // Check if it's toggled
        $action_item->refresh();
        $this->assertNull($action_item->deadline);
    }

    /**
     * Tests editing the goal categories
     *
     * @return void
     * @test
     */
    public function testEditCategories()
    {
        // Create test user
        $user = User::factory()->create();

        // Create goal action item
        $category = GoalCategory::factory()->create(['user_id' => $user->id]);

        // Call the edit page
        $response = $this->actingAs($user)->get(route('goals.edit.categories'));
        $response->assertOk();
        $response->assertSee($category->name);

        // Call create new category
        $response = $this->actingAs($user)->followingRedirects()->post(route('goals.store.category'), [
            '_token' => csrf_token(),
            'name' => 'test',
        ]);
        $response->assertOk();
        $response->assertSee('test');
    }

    // Edit goal/action item

    /**
     * Tests editing a goal
     *
     * @return void
     * @test
     */
    public function testEditGoal()
    {
        // Create test user
        $user = User::factory()->create();

        // Create a goal
        $goal = Goal::factory()->parent()->create(['user_id' => $user->id, 'achieved' => false]);

        // Now see if we can get the edit form
        $response = $this->actingAs($user)->get(route('goals.edit.goal', ['goal' => $goal->uuid]));
        $response->assertOk();
        $response->assertSee('Edit Goal');

        // Test the edit route
        $name = 'Whatever';
        $start_date = Carbon::now();
        $end_date = $start_date->addDay()->format('Y-m-d');
        $start_date = $start_date->subDay()->format('Y-m-d');
        $reason = 'Blah blah blah';
        $response = $this->actingAs($user)->followingRedirects()->post(route('goals.update.goal', ['goal' => $goal->uuid]), [
            '_token' => csrf_token(),
            'category' => 'no-category',
            'type' => 1,
            'name' => $name,
            'start-date' => $start_date,
            'end-date' => $end_date,
            'reason' => $reason,
        ]);

        $goal = Goal::where('user_id', $user->id)->first();
        $this->assertTrue(!is_null($goal));
        $this->assertEquals($goal->name, $name);
        $this->assertEquals($goal->start_date, $start_date);
        $this->assertEquals($goal->end_date, $end_date);
        $this->assertEquals($goal->reason, $reason);
    }

    /**
     * Tests editing a goal
     *
     * @return void
     * @test
     */
    public function testEditActionItem()
    {
        // Create test user
        $user = User::factory()->create();

        // Create goal we can put action items under
        $goal = Goal::factory()->adHoc()->create(['user_id' => $user->id, 'achieved' => false]);
        
        // Create an action item
        $action_item = GoalActionItem::factory()->create([
            'goal_id' => $goal->id,
            'deadline' => null,
            'achieved' => false
        ]);

        // See we get the action item form
        $response = $this->actingAs($user)->get(route('goals.edit.action-item', ['action_item' => $action_item->uuid]));
        $response->assertOk();
        $response->assertSee("Edit&nbsp;$goal->name Action Item", false);

        // Test the update route
        $name = 'Whatever';
        $response = $this->actingAs($user)->followingRedirects()->post(route('goals.update.action-item', ['action_item' => $action_item->uuid]), [
            '_token' => csrf_token(),
            'name' => $name,
        ]);
        $action_item->refresh();
        $this->assertTrue(!is_null($action_item));
        $this->assertEquals($action_item->name, $name);
    }

    /**
     * Tests shifting goal/action item dates
     *
     * @return void
     * @test
     */
    public function testShiftDates()
    {
        // Create test user
        $user = User::factory()->create();

        // Create dates
        $start_date = Carbon::now();
        $end_date = Carbon::now()->addDays(15);
        $deadline = Carbon::now()->addDays(5);

        // Create goal we can put action items under
        $goal = Goal::factory()->actionPlan()->create([
            'user_id' => $user->id,
            'start_date'=> $start_date->format('Y-m-d'),
            'end_date' => $end_date->format('Y-m-d'),
        ]);
        
        // Create an action item
        $action_item = GoalActionItem::factory()->create([
            'goal_id' => $goal->id,
            'deadline' => $deadline->format('Y-m-d'),
        ]);

        // Shift the goal/action item deadline dates
        $this->actingAs($user)->post(route('goals.shift-dates', ['goal' => $goal->uuid]), [
            '_token' => csrf_token(),
            'shift-days' => 5,
        ]);

        // Check they shifted
        $goal->refresh();
        $action_item->refresh();
        $this->assertEquals($goal->start_date, $start_date->addDays(5)->format('Y-m-d'));
        $this->assertEquals($goal->end_date, $end_date->addDays(5)->format('Y-m-d'));
        $this->assertEquals($action_item->deadline, $deadline->addDays(5)->format('Y-m-d'));
    }

    /**
     * Tests updating progress on a manual goal
     *
     * @return void
     * @test
     */
    public function testUpdateManual()
    {
        // Create test user
        $user = User::factory()->create();

        // Create manual goal
        $goal = Goal::factory()->manual()->create([
            'user_id' => $user->id,
            'custom_times' => 100,
            'manual_completed' => 0,
        ]);

        // Update progress
        $response = $this->actingAs($user)->post(route('goals.update.manual-progress', ['goal' => $goal->uuid]), [
            '_token' => csrf_token(),
            'manual-completed' => 50,
        ]);

        // Check if it's updated
        $goal->refresh();
        $this->assertEquals($goal->progress, 50);
        $this->assertEquals($goal->manual_completed, 50);
    }


    /**
     * Tests destorying a goal
     *
     * @return void
     * @test
     */
    public function testDestoryGoal()
    {
        // Create test user
        $user = User::factory()->create();

        // Create goal we can put action items under
        $goal = Goal::factory()->adHoc()->create(['user_id' => $user->id]);

        // Test destorying the goal
        $response = $this->actingAs($user)->post(route('goals.destroy.goal', ['goal' => $goal->uuid]), [
            '_token' => csrf_token(),
        ]);

        $goal = Goal::where('user_id', $user->id)->first();
        $this->assertNull($goal);
    }

    /**
     * Tests destorying an action item
     *
     * @return void
     * @test
     */
    public function testDestoryActionItem()
    {
        // Create test user
        $user = User::factory()->create();

        // Create goal we can put action items under
        $goal = Goal::factory()->adHoc()->create(['user_id' => $user->id]);

        $action_items = GoalActionItem::where('goal_id', $goal->id)->get();

        foreach($action_items as $action_item)
        {
            // Test destorying the goal
            $response = $this->actingAs($user)->post(route('goals.destroy.action-item', ['action_item' => $action_item->uuid]), [
                '_token' => csrf_token(),
            ]);
        }

        $action_item = GoalActionItem::where('goal_id', $goal->id)->first();
        $this->assertNull($action_item);
    }

    /**
     * Tests destorying a category
     *
     * @return void
     * @test
     */
    public function testDestoryCategory()
    {
        // Create test user
        $user = User::factory()->create();

        // Create goal action item
        $category = GoalCategory::factory()->create(['user_id' => $user->id]);

        // Test destorying the goal
        $response = $this->actingAs($user)->post(route('goals.destroy.category', ['category' => $category->uuid]), [
            '_token' => csrf_token(),
        ]);

        $category = GoalCategory::where('user_id', $user->id)->first();
        $this->assertNull($category);
    }
}
