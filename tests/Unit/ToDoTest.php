<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Habits\Type as HabitType;
use App\Helpers\Constants\ToDo\Type;

// Models
use App\Models\Habits\Habits;
use App\Models\User\User;
use App\Models\ToDo\ToDo;
use App\Models\ToDo\ToDoCategory;

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
        do
        {
            $fake_todo = ToDo::factory()->create();
        } while($fake_todo->reminders->count() < 1);
        $uuid = $fake_todo->uuid;
        $fake_reminder = $fake_todo->reminders->first();
        $reminder_uuid = $fake_reminder->uuid;
        $this->assertTrue($fake_reminder->delete());
        $this->assertTrue($fake_todo->delete());

        // Test delete reminder route
        $response = $this->actingAs($user)->post(route('todo.destroy.reminder', ['reminder' => $reminder_uuid]));
        $response->assertStatus(404);

        // Test edit route
        $response = $this->actingAs($user)->get(route('todo.edit', ['todo' => $uuid]));
        $response->assertStatus(404);

        // Test view details route
        $response = $this->actingAs($user)->get(route('todo.view.details', ['todo' => $uuid]));
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

        // Generate a fake todo category and test uuid
        $fake_todo_category = ToDoCategory::factory()->create();
        $uuid = $fake_todo_category->uuid;
        $this->assertTrue($fake_todo_category->delete());

        // Test destroy route
        $response = $this->actingAs($user)->post(route('todo.destroy.category', ['category' => $uuid]));
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

        // Generate a real todo with recurring habit type
        $recurring_habit_todo =
            ToDo::factory()->create([
                'user_id' => $test_user->id,
                'type_id' => Type::RECURRING_HABIT_ITEM,
            ]);

        // Generate a real todo with singular habit type
        $singular_habit_todo =
            ToDo::factory()->create([
                'user_id' => $test_user->id,
                'type_id' => Type::SINGULAR_HABIT_ITEM,
            ]);

        // Generate a real todo with normal type
        $todo =
            ToDo::factory()->create([
                'user_id' => $test_user->id,
                'type_id' => Type::TODO_ITEM,
            ]);

        // Generate a real todo that is completed
        $todo =
            ToDo::factory()->create([
                'user_id' => $test_user->id,
                'type_id' => Type::TODO_ITEM,
                'completed' => true,
            ]);

        // Get a forbidden UUID
        $todos = ToDo::where('user_id', $forbidden_user->id)->get();
        foreach($todos as $f_t)
        {
            $uuid = $f_t->uuid;
            if($f_t->reminders->count() > 0)
            {
                $reminder_uuid = $f_t->reminders->first()->uuid;
                break;
            }
        }

        // Test delete reminder route
        $response = $this->actingAs($test_user)->post(route('todo.destroy.reminder', ['reminder' => $reminder_uuid]));
        $response->assertStatus(403);

        // Test edit route
        $response = $this->actingAs($test_user)->get(route('todo.edit', ['todo' => $uuid]));
        $response->assertStatus(403);

        // Test view details route
        $response = $this->actingAs($test_user)->get(route('todo.view.details', ['todo' => $uuid]));
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

        // Verify recurring habit todo can't hit todo.update or todo.destroy
        $response = $this->actingAs($test_user)->post(route('todo.update', ['todo' => $recurring_habit_todo->uuid]));
        $response->assertStatus(403);

        $response = $this->actingAs($test_user)->post(route('todo.destroy', ['todo' => $recurring_habit_todo->uuid]));
        $response->assertStatus(403);

        // Verify singular habit todo can't hit todo.update
        $response = $this->actingAs($test_user)->post(route('todo.update', ['todo' => $singular_habit_todo->uuid]));
        $response->assertStatus(403);

        // Verify normal todo can't hit todo.update.habit
        $response = $this->actingAs($test_user)->post(route('todo.update.habit', ['todo' => $todo->uuid]));
        $response->assertStatus(403);

        // Verify completed todo can't hit todo.edit, todo.update, or todo.update.habit
        $response = $this->actingAs($test_user)->get(route('todo.edit', ['todo' => $uuid]));
        $response->assertStatus(403);

        $response = $this->actingAs($test_user)->post(route('todo.update', ['todo' => $todo->uuid]));
        $response->assertStatus(403);

        $response = $this->actingAs($test_user)->post(route('todo.update.habit', ['todo' => $todo->uuid]));
        $response->assertStatus(403);

        // Generate a forbidden todo category and test uuid
        $forbidden_todo_category = ToDoCategory::factory()->create([
            'user_id' => $forbidden_user->id
        ]);
        $uuid = $forbidden_todo_category->uuid;

        // Test destroy route
        $response = $this->actingAs($test_user)->post(route('todo.destroy.category', ['category' => $uuid]));
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
        $response->assertStatus(200);
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
        $response->assertStatus(200);
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
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2> Create New Item </h2>', false);
        $response->assertSee('action="' . route('todo.store') . '"', false);
        $response->assertSee('<input type="text" name="title" placeholder="Title" maxlength="255"', false);
        $response->assertSee('<div class="priority-container">', false);
        $response->assertSee('placeholder="Any notes for your to-do item go here!"', false);
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
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2> Edit Item </h2>', false);
        $response->assertSee('action="' . route('todo.update', ['todo' => $item->uuid]) . '"', false);
        $response->assertSee('<input type="text" name="title" placeholder="Title" maxlength="255"', false);
        $response->assertSee('value="' . $item->title . '"', false);
        $response->assertSee('<div class="priority-container">', false);
        $response->assertSee('placeholder="Any notes for your to-do item go here!"', false);
        $response->assertSee('>' . $item->notes . '</textarea>', false);
        $response->assertSee('<button class="submit" type="submit">Submit</button>', false);
    }

    /**
     * Tests editing a completed item
     *
     * @return void
     * @test
     */
    public function testViewCompleted()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it a to do item that has been completed
        $item = ToDo::factory()->create([
            'user_id' => $user->id,
            'completed' => true
        ]);

        // Get response
        $response = $this->actingAs($user)->get(route('todo.view.details', ['todo' => $item->uuid]));
        $response->assertStatus(200);

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
     * Tests that the to do update route works
     *
     * @return void
     * @test
     */
    public function testUpdate()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it a to do item
        $item = ToDo::factory()->create([
            'user_id' => $user->id,
            'completed' => false,
        ]);

        // Send data to create a new to-do item
        $response = $this->actingAs($user)->post(route('todo.update', ['todo' => $item->uuid]), [
            '_token' => csrf_token(),
            'title' => 'Test Item',
            'category' => 'no-category',
            'priority-3' => 'on',
            'notes' => 'This item is testing the update route.'
        ]);

        // Verify redirected back to to do list properly
        $response->assertRedirect("/todo/view/$item->uuid");

        // Refresh model
        $item->refresh();

        $this->assertEquals($item->title, 'Test Item');
        $this->assertEquals($item->priority_id, 3);
        $this->assertEquals($item->notes, 'This item is testing the update route.');
    }

    /**
     * Tests that the store route works
     *
     * @return void
     * @test
     */
    public function testStore()
    {
        // Create test user
        $user = User::factory()->create();

        // Send data to create a new to-do item
        $response = $this->actingAs($user)->post(route('todo.store'), [
            '_token' => csrf_token(),
            'title' => 'Test Item',
            'category' => 'no-category',
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

    /**
     * Tests creating, assigning, and displaying a todo category
     *
     * @return void
     * @test
     */
    public function testCreateCategory()
    {
        // Create test user
        $user = User::factory()->create();

        // Create a todo items
        $todo_item = ToDo::factory()->create([
            'user_id' => $user->id,
        ]);

        // Call the edit categories route
        $response = $this->actingAs($user)->get(route('todo.edit.categories'));
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2>Edit Categories</h2>', false);
        $response->assertSee('<input type="text" name="name" placeholder="Add a new category" maxlength="255" />', false);
        $response->assertSee('<button class="add" type="submit">Add</button>', false);

        // Create a category
        $response = $this->actingAs($user)->post(route('todo.store.category'), [
            '_token' => csrf_token(),
            'name' => 'Test Category',
        ]);

        // Verify redirected back to the categories page properly
        $response->assertRedirect('/todo/edit/categories');

        // Verify it shows up on the edit categories page now
        $response = $this->actingAs($user)->get(route('todo.edit.categories'));
        $response->assertStatus(200);
        $response->assertSee('Test Category');

        // Refresh use to get the category id
        $user->refresh();
        $category = $user->todoCategories()->first();

        // Assign it to a todo item
        $todo_item->category_id = $category->id;
        $this->assertTrue($todo_item->save());

        // Check the todo page to see if the category select shows up
        $response = $this->actingAs($user)->get(route('todo.list'));
        $response->assertStatus(200);
        $response->assertSee($category->uuid);
    }

    /**
     * Tests that the delete category route works
     *
     * @return void
     * @test
     */
    public function testDestroyCategory()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it a to do category
        $category = ToDoCategory::factory()->create([
            'user_id' => $user->id,
        ]);

        // Verify it has a category
        $this->assertEquals($user->todoCategories()->get()->count(), 1);

        // Send data to delete to-do category
        $response = $this->actingAs($user)->post(route('todo.destroy.category', ['category' => $category->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Verify redirected back to the manage todo categories page properly
        $response->assertRedirect('/todo/edit/categories');

        // Refresh user and get todo categories
        $user->refresh();
        $category = $user->todoCategories()->first();

        // Verify it's not returned now
        $this->assertNull($category);
    }

    /**
     * Tests that the affirmations/journaling habits push to the todo list
     *
     * @return void
     * @test
     */
    public function testShowSpecialHabits()
    {
        // Create test user
        $user = User::factory()->create();

        // Generate affirmations habit
        $response = $this->actingAs($user)->get(route('habits'));
        $response->assertRedirect('/habits');
        $affirmations_habit = Habits::where('user_id', $user->id)->where('type_id', HabitType::AFFIRMATIONS_HABIT)->first();
        $this->assertIsObject($affirmations_habit);

        // Generate journaling habit
        $response = $this->actingAs($user)->get(route('habits'));
        $response->assertRedirect('/habits');
        $journaling_habit = Habits::where('user_id', $user->id)->where('type_id', HabitType::JOURNALING_HABIT)->first();
        $this->assertIsObject($journaling_habit);

        // Set affirmations habit to show up on todo list
        $affirmations_habit->show_todo = true;
        $this->assertTrue($affirmations_habit->save());

        // Set journling habit to show up on todo list
        $journaling_habit->show_todo = true;
        $this->assertTrue($journaling_habit->save());

        // Check todo list for both affirmations and journaling habit todos
        $response = $this->actingAs($user)->get(route('todo.list'));
        $response->assertStatus(200);
        $response->assertSee('Journaling Habit');
        $response->assertSee('Affirmations Habit (9 more times)');
    }

    /**
     * Tests creating a todo reminder
     *
     * @return void
     * @test
     */
    public function testReminders()
    {
        // Create test user
        $user = User::factory()->create();

        // Create a todo item
        do
        {
            $todo_item = ToDo::factory()->create([
                'user_id' => $user->id,
            ]);
        } while($todo_item->reminders->count() < 1);

        // Delete reminders
        foreach($todo_item->reminders as $reminder)
        {
            // Send data to delete to-do item
            $response = $this->actingAs($user)->post(route('todo.destroy.reminder', ['reminder' => $reminder->uuid]), [
                '_token' => csrf_token(),
            ]);

            // Verify redirected back to to do list properly
            $response->assertRedirect('/todo/edit/reminders/' . $todo_item->uuid);
        }

        $todo_item->refresh();
        $this->assertTrue($todo_item->reminders->count() == 0);

        // Call the edit reminders route
        $response = $this->actingAs($user)->get(route('todo.edit.reminders', ['todo' => $todo_item->uuid]));
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2>Edit Reminders</h2>', false);
        $response->assertSee('action="' . route('todo.store.reminder', ['todo' => $todo_item->uuid]), false);
        $response->assertSee('<button class="add" type="submit">Add</button>', false);

        // Create a reminder
        $response = $this->actingAs($user)->post(route('todo.store.reminder', ['todo' => $todo_item->uuid]), [
            '_token' => csrf_token(),
            'date' => '2021-09-24',
            'time' => '14:17',
        ]);

        // Verify redirected back to the reminders page properly
        $response->assertRedirect('/todo/edit/reminders/' . $todo_item->uuid);

        // Verify it shows up on the edit categories page now
        $response = $this->actingAs($user)->get(route('todo.edit.reminders', ['todo' => $todo_item->uuid]));
        $response->assertStatus(200);
        $response->assertSee('Fri, Sep 24 @ 2:17 PM');

        // Refresh to check the reminder is there now
        $todo_item->refresh();
        $this->assertTrue($todo_item->reminders->count() > 0);
    }
}
