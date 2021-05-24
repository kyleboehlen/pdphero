<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Habits\HistoryType;

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


    /**
     * Tests the create form shows properly
     *
     * @return void
     * @test
     */
    public function testCreateForm()
    {
        // Create test user
        $user = User::factory()->create();

        // Get response
        $response = $this->actingAs($user)->get(route('habits.create'));
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2> Create New Habit </h2>', false);
        $response->assertSee('<form class="habit"  action="' . route('habits.store') . '"  method="POST">', false);
        $response->assertSee('<input type="text" name="title" placeholder="Title" maxlength="255"', false);
        $response->assertSee('<div class="required-on">', false);
        $response->assertSee('<div class="day-of-week-container">', false);
        $response->assertSee('<p class="every-x-days  disabled " required>', false);
        $response->assertSee('<input type="number" name="times-daily" min="1" max="100" required', false);
        $response->assertSee('<textarea name="notes" placeholder="Any notes for your habit go here!"></textarea>', false);
        $response->assertSee('<button class="submit" type="submit">Submit</button>', false);
    }

    /**
     * Tests the create route works
     *
     * @return void
     * @test
     */
    public function testStore()
    {
        // Create some basic habit values
        $title = 'Fuck a duck';
        $times_daily = '3'; // I'm not sure if that's 3 ducks orr...
        $notes = 'It\'s a fucking idiom, calm down PETA.';

        // Create test user
        $user = User::factory()->create();

        // Send data to create a new days of the week habit
        $days_of_week_array = ['0', '2', '5']; // Sunday, Tuesday, Friday
        $response = $this->actingAs($user)->post(route('habits.store'), [
            '_token' => csrf_token(),
            'title' => $title,
            'times-daily' => $times_daily,
            'days-of-week' => $days_of_week_array,
            'notes' => $notes,
        ]);

        // Verify redirected back to habits index
        $response->assertRedirect('/habits');

        // Get that habit
        $habit = Habits::where('user_id', $user->id)->first();

        $this->assertEquals($habit->name, $title); // this is kinda confusing, but whatevessss
        $this->assertEquals($habit->times_daily, $times_daily);
        $this->assertEquals($habit->notes, $notes);

        // Verify they show up on the index page
        $response = $this->actingAs($user)->followingRedirects()->get(route('habits'));
        $response->assertOk();
        $response->assertSee($title);
        
        // And the view details page
        $response = $this->actingAs($user)->get(route('habits.view', ['habit' => $habit->uuid]));
        $response->assertOk();
        $response->assertSee($title);
        $response->assertSee($notes);
        $response->assertSee('Sunday, Tuesday, Friday');
        $response->assertSee("Required $times_daily times");

        // do it again with every x days
        $user = User::factory()->create();

        // Send data to create a new days of the week habit
        $every_x_days_value = 8;
        $response = $this->actingAs($user)->post(route('habits.store'), [
            '_token' => csrf_token(),
            'title' => $title,
            'times-daily' => 1, // Default
            'every-x-days' => $every_x_days_value,
        ]);

        // Verify redirected back to habits index
        $response->assertRedirect('/habits');

        // Get that habit
        $habit = Habits::where('user_id', $user->id)->first();

        $this->assertEquals($habit->name, $title);
        $this->assertEquals($habit->times_daily, 1);
        $this->assertNull($habit->notes);

        // Verify they show up on the index page
        $response = $this->actingAs($user)->followingRedirects()->get(route('habits'));
        $response->assertOk();
        $response->assertSee($title);
        
        // And the view details page
        $response = $this->actingAs($user)->get(route('habits.view', ['habit' => $habit->uuid]));
        $response->assertOk();
        $response->assertSee($title);
        $response->assertSee("every $every_x_days_value days");
    }

    /**
     * Tests the create form shows properly
     *
     * @return void
     * @test
     */
    public function testEditForm()
    {
        // Create test user
        $user = User::factory()->create();

        // Create both types of habits
        $notes = 'Fuck a duck';
        $days_of_week_array = [0, 2, 5]; // Monday, Wednesday, Saturday
        $days_of_week_habit = Habits::factory()->create([
            'user_id' => $user->id,
            'days_of_week' => $days_of_week_array,
            'every_x_days' => null,
            'show_todo' => true,
            'notes' => $notes,
        ]);
        $every_x_days_value = 5;
        $every_x_days_habit = Habits::factory()->create([
            'user_id' => $user->id,
            'days_of_week' => null,
            'every_x_days' => $every_x_days_value,
            'show_todo' => false,
            'notes' => $notes,
        ]);

        // Test editing day of week habit
        $response = $this->actingAs($user)->get(route('habits.edit', ['habit' => $days_of_week_habit->uuid]));
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2> Edit Habit </h2>', false);
        $response->assertSee('<form class="habit"  action="' . route('habits.update', ['habit' => $days_of_week_habit->uuid]) . '"  method="POST">', false);
        $response->assertSee('<input type="text" name="title" placeholder="Title" maxlength="255"', false);
        $response->assertSee($days_of_week_habit->name);
        $response->assertSee('<div class="required-on">', false);
        $response->assertSee('<div class="day-of-week-container">', false);
        $response->assertSee('<p class="every-x-days   disabled  " required>', false);
        $response->assertSee('<input class="show-todo" type="checkbox" name="show-todo"   checked   />', false);
        $response->assertSee('<input type="number" name="times-daily" min="1" max="100" required', false);
        $response->assertSee('<textarea name="notes" placeholder="Any notes for your habit go here!">' . $notes . '</textarea>', false);
        $response->assertSee('<button class="submit" type="submit">Submit</button>', false);

        // again with every x days
        $response = $this->actingAs($user)->get(route('habits.edit', ['habit' => $every_x_days_habit->uuid]));
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2> Edit Habit </h2>', false);
        $response->assertSee('<form class="habit"  action="' . route('habits.update', ['habit' => $every_x_days_habit->uuid]) . '"  method="POST">', false);
        $response->assertSee('<input type="text" name="title" placeholder="Title" maxlength="255"', false);
        $response->assertSee($every_x_days_habit->name);
        $response->assertSee('<div class="required-on">', false);
        $response->assertSee('<div class="day-of-week-container">', false);
        $response->assertSee('<p class="every-x-days   " required>', false);
        $response->assertSee('<input class="show-todo" type="checkbox" name="show-todo"    />', false);
        $response->assertSee('value="' . $every_x_days_value . '"', false);
        $response->assertSee('value="1"', false);
        $response->assertSee('<textarea name="notes" placeholder="Any notes for your habit go here!">' . $notes . '</textarea>', false);
        $response->assertSee('<button class="submit" type="submit">Submit</button>', false);
    }

    /**
     * Tests the update route works
     *
     * @return void
     * @test
     */
    public function testUpdate()
    {
        // Create some basic habit values
        $title = 'Fuck a duck';
        $times_daily = '3'; // I'm not sure if that's 3 ducks orr...
        $notes = 'It\'s a fucking idiom, calm down PETA.';

        // Create test user
        $user = User::factory()->create();

        // Create a test habit
        $habit = Habits::factory()->create([
            'user_id' => $user->id,
        ]);

        // Send data to update that habit
        $days_of_week_array = ['0', '2', '5']; // Sunday, Tuesday, Friday
        $response = $this->actingAs($user)->post(route('habits.update', ['habit' => $habit->uuid]), [
            '_token' => csrf_token(),
            'title' => $title,
            'times-daily' => $times_daily,
            'days-of-week' => $days_of_week_array,
            'notes' => $notes,
        ]);

        // Verify redirected back to habit view
        $response->assertRedirect("habits/view/$habit->uuid");

        // Refresh model
        $habit->refresh();

        $this->assertEquals($habit->name, $title); // this is kinda confusing, but whatevessss
        $this->assertEquals($habit->times_daily, $times_daily);
        $this->assertEquals($habit->notes, $notes);

        // Verify they show up on the index page
        $response = $this->actingAs($user)->followingRedirects()->get(route('habits'));
        $response->assertOk();
        $response->assertSee($title);
        
        // And the view details page
        $response = $this->actingAs($user)->get(route('habits.view', ['habit' => $habit->uuid]));
        $response->assertOk();
        $response->assertSee($title);
        $response->assertSee($notes);
        $response->assertSee('Sunday, Tuesday, Friday');
        $response->assertSee("Required $times_daily times");

        // do it again with every x days
        $user = User::factory()->create();

        // Create a test habit
        $habit = Habits::factory()->create([
            'user_id' => $user->id,
        ]);

        // Send data to create a new days of the week habit
        $every_x_days_value = 8;
        $response = $this->actingAs($user)->post(route('habits.update', ['habit' => $habit->uuid]), [
            '_token' => csrf_token(),
            'title' => $title,
            'times-daily' => 1, // Default
            'every-x-days' => $every_x_days_value,
        ]);

        // Verify redirected back to habit view
        $response->assertRedirect("habits/view/$habit->uuid");

        // Refresh model
        $habit->refresh();

        $this->assertEquals($habit->name, $title);
        $this->assertEquals($habit->times_daily, 1);

        // Verify they show up on the index page
        $response = $this->actingAs($user)->followingRedirects()->get(route('habits'));
        $response->assertOk();
        $response->assertSee($title);
        
        // And the view details page
        $response = $this->actingAs($user)->get(route('habits.view', ['habit' => $habit->uuid]));
        $response->assertOk();
        $response->assertSee($title);
        $response->assertSee("every $every_x_days_value days");
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

        // Create a test habit
        $habit = Habits::factory()->create([
            'user_id' => $user->id,
        ]);

        // Send data to delete to-do item
        $response = $this->actingAs($user)->post(route('habits.destroy', ['habit' => $habit->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Verify redirected back to index properly
        $response->assertRedirect('/habits');

        // Get that to do item
        $habit = Habits::where('user_id', $user->id)->first();

        // Verify it's not returned now
        $this->assertNull($habit);
    }

    /**
     * Tests that the index page shows properly
     *
     * @return void
     * @test
     */
    public function testIndex()
    {
        // Create test user
        $user = User::factory()->create();

        // Generate fake habits for user
        $habits = Habits::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Get index and verify they are all there, no need to test for affirmations it is done in SettingsTest
        $response = $this->actingAs($user)->followingRedirects()->get(route('habits'));
        $response->assertOk();
        foreach($habits as $habit)
        {
            $response->assertSee($habit->name);
        }
    }

    /**
     * Tests the color guide shows properly
     *
     * @return void
     * @test
     */
    public function testColors()
    {
        // Create test user
        $user = User::factory()->create();

        // Verify its working
        $response = $this->actingAs($user)->followingRedirects()->get(route('habits.colors'));
        $response->assertOk();
        $response->assertSee('Gradient');
        $response->assertSee('Solid');
        $response->assertSee('Brown');
        $response->assertSee('Red');
        $response->assertSee('Yellow');
        $response->assertSee('Green');
    }

    /**
     * Tests updating history
     *
     * @return void
     * @test
     */
    public function testHistory()
    {
        // Create a test user (keep in mind rolling seven days is default)
        $user = User::factory()->create();

        // Set timezone
        $user->timezone = 'America/Denver';
        $this->assertTrue($user->save());
        $user->refresh();

        // Create a date to play with and set the required/not required days
        $now = new Carbon('now', $user->timezone);
        if($now->format('w') == 5 || $now->format('w') == 6) // Friday/Saturday
        {
            $required_days = [0, 1, 2, 3];
            $not_required_days = [4, 5, 6];
        }
        else
        {
            $required_days = [0, 1, 2, 3, 4];
            $not_required_days = [5, 6];
        }
        $now->subDays(6);

        // Create a habit, lets go with days of week to make things easy
        $habit = Habits::factory()->create([
            'user_id' => $user->id,
            'days_of_week' => $required_days,
            'every_x_days' => null,
            'times_daily' => 3,
        ]);

        // These are the keys we'll be verifying against the status in habits history array
        $completed_key = null;
        $partial_key = null;
        $skipped_key = null;
        $missed_key = null;
        $not_required_missed_key = null;
        $not_required_completed_key = null;

        do
        {
            // Update a required day to completed
            if(is_null($completed_key) && in_array($now->format('w'), $required_days))
            {
                $completed_key = $now->format('w');
                $response = $this->actingAs($user)->post(route('habits.history', ['habit' => $habit->uuid]), [
                    '_token' => csrf_token(),
                    'day' => $now->format('Y-m-d'),
                    'status-completed' => 'checked',
                    'times' => 3,
                ]);
                $now->addDay();
                continue;
            }

            // Update a required day to partial
            if(is_null($partial_key) && in_array($now->format('w'), $required_days))
            {
                $partial_key = $now->format('w');
                $response = $this->actingAs($user)->post(route('habits.history', ['habit' => $habit->uuid]), [
                    '_token' => csrf_token(),
                    'day' => $now->format('Y-m-d'),
                    'status-completed' => 'checked',
                    'times' => 1,
                ]);
                $now->addDay();
                continue;
            }

            // Update a required day to skipped
            if(is_null($skipped_key) && in_array($now->format('w'), $required_days))
            {
                $skipped_key = $now->format('w');
                $response = $this->actingAs($user)->post(route('habits.history', ['habit' => $habit->uuid]), [
                    '_token' => csrf_token(),
                    'day' => $now->format('Y-m-d'),
                    'status-skipped' => 'checked',
                    'notes' => 'Learn about our enhanced health and safety measures',
                ]);
                $now->addDay();
                continue;
            }

            // Update a required day to missed
            if(is_null($missed_key) && in_array($now->format('w'), $required_days))
            {
                $missed_key = $now->format('w');
                $response = $this->actingAs($user)->post(route('habits.history', ['habit' => $habit->uuid]), [
                    '_token' => csrf_token(),
                    'day' => $now->format('Y-m-d'),
                    'status-missed' => 'checked',
                ]);
                $now->addDay();
                continue;
            }

            // Update a not required day to missed
            if(is_null($not_required_missed_key) && in_array($now->format('w'), $not_required_days))
            {
                $not_required_missed_key = $now->format('w');
                $response = $this->actingAs($user)->post(route('habits.history', ['habit' => $habit->uuid]), [
                    '_token' => csrf_token(),
                    'day' => $now->format('Y-m-d'),
                    'status-missed' => 'checked',
                ]);
                $now->addDay();
                continue;
            }

            // Update a not required day to completed
            if(is_null($not_required_completed_key) && in_array($now->format('w'), $not_required_days))
            {
                $not_required_completed_key = $now->format('w');
                $response = $this->actingAs($user)->post(route('habits.history', ['habit' => $habit->uuid]), [
                    '_token' => csrf_token(),
                    'day' => $now->format('Y-m-d'),
                    'status-completed' => 'checked',
                    'times' => 3,
                ]);
                $now->addDay();
                continue;
            }
        }
        while(
            is_null($completed_key) ||
            is_null($partial_key) ||
            is_null($skipped_key) ||
            is_null($missed_key) ||
            is_null($not_required_missed_key) ||
            is_null($not_required_completed_key)
        );

        // Get history array for habit -- default seven rolling days
        $history_array = $habit->getHistoryArray();

        // Test each key
        $this->assertEquals(HistoryType::COMPLETED, $history_array[$completed_key]['status']);
        $this->assertEquals(HistoryType::PARTIAL, $history_array[$partial_key]['status']);
        $this->assertEquals(HistoryType::SKIPPED, $history_array[$skipped_key]['status']);
        $this->assertEquals(HistoryType::MISSED, $history_array[$missed_key]['status']);
        $this->assertEquals(HistoryType::SKIPPED, $history_array[$not_required_missed_key]['status']);
        $this->assertEquals(HistoryType::COMPLETED, $history_array[$not_required_completed_key]['status']);
    }

    /**
     * The integration that pushes habits to the to-do list
     *
     * @return void
     * @test
     */
    public function testToDo()
    {
        // Create a test user
        $user = User::factory()->create();

        // Create a test habit required 2 times daily that pushes to todo list
        $habit = Habits::factory()->create([
            'user_id' => $user->id,
            'days_of_week' => null,
            'every_x_days' => 1,
            'times_daily' => 2,
            'show_todo' => true,
        ]);

        // Check if todo item shows up on list
        $response = $this->actingAs($user)->get(route('todo.list'));
        $response->assertStatus(200);
        $response->assertSee("$habit->name (2 more times)");

        // Toggle the todo item
        foreach($habit->recurringTodos as $todo)
        {
            if(!$todo->completed)
            {
                $todo->toggleCompleted();
            }
        }

        // Check if both todo items show up now
        $response = $this->actingAs($user)->get(route('todo.list'));
        $response->assertStatus(200);
        $response->assertSee("$habit->name (1 more time)");
        $response->assertSee("$habit->name (1 out of 2)");

        // Check if habit history day shows up as partial
        $habit->refresh();
        $history_array = $habit->getHistoryArray();
        $this->assertEquals(end($history_array)['status'], HistoryType::PARTIAL);

        // Toggle the todo item
        foreach($habit->recurringTodos as $todo)
        {
            if(!$todo->completed)
            {
                $todo->toggleCompleted();
            }
        }

        // Toggle the todo item
        foreach($habit->recurringTodos as $todo)
        {
            if(!$todo->completed)
            {
                $todo->toggleCompleted();
            }
        }

        // Make sure just completed todo shows up
        $response = $this->actingAs($user)->get(route('todo.list'));
        $response->assertStatus(200);
        $response->assertSee("$habit->name");
    }
}
