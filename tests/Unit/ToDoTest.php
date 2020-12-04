<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

// Models
use App\Models\User\User;
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

    /**
     * Tests that a user with to do items loads the proper items
     *
     * @return void
     * @test
     */
    public function testToDoList()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it to do items
        ToDo::factory(rand(15, 60))->create([
            'user_id' => $user->id,
        ]);

        // Default hours to keep a completed to do item on the list
        $completed_at = Carbon::now()->subDay()->toDatetimeString();
        
        // Get users To do items
        $to_do_items = Todo::where('user_id', $user->id)->where(function($q) use ($completed_at){
            $q->where('completed', 0)->orWhere(function($s_q) use ($completed_at){ // Is either incomplete
                $s_q->where('completed', 1)->where('updated_at', '>=', $completed_at); // or is complete and within the hours to display completed for user
            });
        })->get();

        // Verify list has each of the to do items
        $response = $this->actingAs($user)->get(route('todo.list'));
        foreach($to_do_items as $item)
        {
            $response->assertSee($item->title);
        }
    }

    /**
     * Tests that a user with no todo items has the create item prompt
     *
     * @return void
     * @test
     */
    public function testEmptyToDoList()
    {
        // Create test user
        $user = User::factory()->create();

        // Get response
        $response = $this->actingAs($user)->get(route('todo.list'));
        $response->assertSee('Create a new To-Do Item');
    }

    /**
     * Tests that the create route loads the proper form
     *
     * @return void
     * @test
     */
    public function testCreateForm()
    {
        // Create test user
        $user = User::factory()->create();

        // Get response
        $response = $this->actingAs($user)->get(route('todo.create'));

        // Check for various important parts of the form
        $response->assertSee('<h2>Create New Item</h2>', false);
        $response->assertSee('<form class="to-do"  action="' . route('todo.store') . '"  method="POST">', false);
        $response->assertSee('<input type="text" name="title" placeholder="Title" maxlength="255"  value=""  required />', false);
        $response->assertSee('<div class="priority-container">', false);
        $response->assertSee('<textarea name="notes" placeholder="Any notes for your to-do item go here!"></textarea>', false);
        $response->assertSee('<button class="submit" type="submit">Submit</button>', false);
    }

    /**
     * Tests that the edit form loads properly
     *
     * @return void
     * @test
     */
    public function testEditForm()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it a to do item that has not been completed
        $item = ToDo::factory()->create([
            'user_id' => $user->id,
            'completed' => false
        ]);

        // Get response
        $response = $this->actingAs($user)->get(route('todo.edit', ['todo' => $item->uuid]));

        // Check for various important parts of the form
        $response->assertSee('<h2>Edit Item</h2>', false);
        $response->assertSee('<form class="to-do"  action="' . route('todo.update', ['todo' => $item->uuid]) . '"  method="POST">', false);
        $response->assertSee('<input type="text" name="title" placeholder="Title" maxlength="255"  value="' . $item->title . '"  required />', false);
        $response->assertSee('<div class="priority-container">', false);
        $response->assertSee('<textarea name="notes" placeholder="Any notes for your to-do item go here!">' . $item->notes . '</textarea>', false);
        $response->assertSee('<button class="submit" type="submit">Submit</button>', false);
    }

    /**
     * Tests editing a completed item
     *
     * @return void
     * @test
     */
    public function testEditCompleted()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it a to do item that has been completed
        $item = ToDo::factory()->create([
            'user_id' => $user->id,
            'completed' => true
        ]);

        // Get response
        $response = $this->actingAs($user)->get(route('todo.edit', ['todo' => $item->uuid]));

        // Check for various important parts of the form
        $response->assertSee('<h2>Completed Item</h2>', false);
        $response->assertSee("Good job completing $item->title");
        if(!is_null($item->notes))
        {
            $response->assertSee($item->notes);
        }
        $response->assertSee('<button type="button">Okay</button>', false);
    }

    /**
     * Tests that the to do store route works
     *
     * @return void
     * @test
     */
    public function testStore()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it a to do item
        $item = ToDo::factory()->create([
            'user_id' => $user->id,
        ]);

        // Send data to create a new to-do item
        $response = $this->actingAs($user)->post(route('todo.update', ['todo' => $item->uuid]), [
            '_token' => csrf_token(),
            'title' => 'Test Item',
            'priority-3' => 'on',
            'notes' => 'This item is testing the update route.'
        ]);

        // Verify redirected back to to do list properly
        $response->assertRedirect('/todo');

        // Refresh model
        $item->refresh();

        $this->assertEquals($item->title, 'Test Item');
        $this->assertEquals($item->priority_id, 3);
        $this->assertEquals($item->notes, 'This item is testing the update route.');
    }

    /**
     * Tests that the update route works
     *
     * @return void
     * @test
     */
    public function testUpdate()
    {
        // Create test user
        $user = User::factory()->create();

        // Send data to create a new to-do item
        $response = $this->actingAs($user)->post(route('todo.store'), [
            '_token' => csrf_token(),
            'title' => 'Test Item',
            'priority-6' => 'on',
            'notes' => 'This item is testing the store route.'
        ]);

        // Verify redirected back to to do list properly
        $response->assertRedirect('/todo');

        // Get that to do item
        $item = ToDo::where('user_id', $user->id)->first();

        $this->assertEquals($item->title, 'Test Item');
        $this->assertEquals($item->priority_id, 6);
        $this->assertEquals($item->notes, 'This item is testing the store route.');
    }

    /**
     * Tests that the delete route works
     *
     * @return void
     * @test
     */
    public function testDestroy()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it a to do item
        $item = ToDo::factory()->create([
            'user_id' => $user->id,
        ]);

        // Send data to delete to-do item
        $response = $this->actingAs($user)->post(route('todo.destroy', ['todo' => $item->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Verify redirected back to to do list properly
        $response->assertRedirect('/todo');

        // Get that to do item
        $item = ToDo::where('user_id', $user->id)->first();

        // Verify it's not returned now
        $this->assertNull($item);
    }

    /**
     * Tests that toggle completed route works
     *
     * @return void
     * @test
     */
    public function testToggleCompleted()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it a to do item that is not completed
        $item = ToDo::factory()->create([
            'user_id' => $user->id,
            'completed' => false
        ]);

        // Send data to toggle the completed status of the to-do item
        $response = $this->actingAs($user)->post(route('todo.toggle-completed', ['todo' => $item->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Verify redirected back to to do list properly
        $response->assertRedirect('/todo');

        // Refresh model
        $item->refresh();

        // Assert it's now completed
        $this->assertTrue((bool) $item->completed);

        // Send data to toggle the completed status of the to-do item
        $response = $this->actingAs($user)->post(route('todo.toggle-completed', ['todo' => $item->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Verify redirected back to to do list properly
        $response->assertRedirect('/todo');

        // Refresh model
        $item->refresh();

        // Assert it's no longer completed
        $this->assertFalse((bool) $item->completed);
    }
}
