<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Habits\HistoryType as HabitHistoryType;

// Models
use App\Models\Affirmations\AffirmationsReadLog;
use App\Models\Goal\Goal;
use App\Models\Goal\GoalActionItem;
use App\Models\Habits\Habits;
use App\Models\Habits\HabitHistory;
use App\Models\Journal\JournalEntry;
use App\Models\Journal\JournalCategory;
use App\Models\ToDo\ToDo;
use App\Models\User\User;

class JournalTest extends TestCase
{
    /**
     * Tests journal UUID routes with fake UUIDs for 404 errors
     *
     * @return void
     * @test
     */
    public function testFakeUUIDs()
    {
        // Create test user
        $user = User::factory()->create();

        // Generate fake journal entries for user
        $journal_entries = JournalEntry::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Generate a fake journal entry and grab the UUID for testing
        $fake_journal_entry = JournalEntry::factory()->create();
        $journal_entry_uuid = $fake_journal_entry->uuid;
        $this->assertTrue($fake_journal_entry->delete());
        
        // Generate fake journal category and grab uuid for testing delete
        $fake_journal_category = JournalCategory::factory()->create();
        $journal_category_uuid = $fake_journal_category->uuid;
        $this->assertTrue($fake_journal_category->delete());

        // Test view route
        $response = $this->actingAs($user)->get(route('journal.view.entry', ['journal_entry' => $journal_entry_uuid]));
        $response->assertStatus(404);

        // Test edit route
        $response = $this->actingAs($user)->get(route('journal.edit.entry', ['journal_entry' => $journal_entry_uuid]));
        $response->assertStatus(404);

        // Test update route
        $response = $this->actingAs($user)->post(route('journal.update.entry', ['journal_entry' => $journal_entry_uuid]));
        $response->assertStatus(404);

        // Test delete route
        $response = $this->actingAs($user)->post(route('journal.destroy.entry', ['journal_entry' => $journal_entry_uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->post(route('journal.destroy.category', ['category' => $journal_category_uuid]));
        $response->assertStatus(404);
    }

    /**
     * Tests journal UUID routes with a UUID that doesn't belong
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
        $journal_entries = JournalEntry::factory(rand(5, 15))->create([
            'user_id' => $forbidden_user->id,
        ]);

        // Get a forbidden UUID
        $journal_entry = $journal_entries->random();
        $journal_entry_uuid = $journal_entry->uuid;

        // Get a forbidden journal category uuid
        $journal_category = JournalCategory::factory()->create([
            'user_id' => $forbidden_user->id,
        ]);
        $journal_category_uuid = $journal_category->uuid;

        // Test view route
        $response = $this->actingAs($test_user)->get(route('journal.view.entry', ['journal_entry' => $journal_entry_uuid]));
        $response->assertStatus(403);

        // Test edit route
        $response = $this->actingAs($test_user)->get(route('journal.edit.entry', ['journal_entry' => $journal_entry_uuid]));
        $response->assertStatus(403);

        // Test update route
        $response = $this->actingAs($test_user)->post(route('journal.update.entry', ['journal_entry' => $journal_entry_uuid]));
        $response->assertStatus(403);

        // Test delete route
        $response = $this->actingAs($test_user)->post(route('journal.destroy.entry', ['journal_entry' => $journal_entry_uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($test_user)->post(route('journal.destroy.category', ['category' => $journal_category_uuid]));
        $response->assertStatus(403);
    }

    /**
     * Tests that viewing the month list and viewing the day
     * view also works
     *
     * @return void
     * @test
     */
    public function testView()
    {
        // Create test user
        $timezone = 'America/Denver';
        $user = User::factory()->create();
        $user->timezone = $timezone;
        $this->assertTrue($user->save());
        $user->refresh();

        // Get current timestamp
        $now = Carbon::now();
        $now->setTimezone($timezone);

        // Create habits
        $habits = Habits::factory(2)->create([
            'user_id' => $user->id,
            'times_daily' => 1,
            'every_x_days' => 1,
        ]);

        foreach($habits as $habit)
        {
            $habit_history = new HabitHistory([
                'habit_id' => $habit->id,
                'type_id' => HabitHistoryType::COMPLETED,
                'day' => $now->format('Y-m-d'),
                'times' => 1,
            ]);
            $this->assertTrue($habit_history->save());
        }

        // Add affirmations read log
        for($i = 0; $i < 9; $i++)
        {
            $affirmations_read_log = new AffirmationsReadLog([
                'user_id' => $user->id,
            ]);
            $this->assertTrue($affirmations_read_log->save());
        }

        // Achieve a goal
        $goal = Goal::factory()->actionPlan()->create([
            'user_id' => $user->id,
            'achieved' => 1,
        ]);

        GoalActionItem::where('goal_id', $goal->id)->delete();

        // Achieve action items
        $action_items = GoalActionItem::factory(3)->create([
            'goal_id' => $goal->id,
            'achieved' => 1,
        ]);

        // Complete todos
        $to_dos = ToDo::factory(10)->create([
            'user_id' => $user->id,
            'completed' => 1,
        ]);

        // Add journal entries
        $journal_entries = JournalEntry::factory(4)->create([
            'user_id' => $user->id,
            'created_at' => $now->toDateTimeString(),
        ]);

        // Check month view
        $response = $this->actingAs($user)->get(route('journal.view.list'));
        $response->assertOk();
        $response->assertSee('<b>2</b> <i>Habits Performed</i>', false);
        $response->assertSee('<b>9</b> <i>Affirmations Read</i>', false);
        $response->assertSee('<b>1</b> <i>Goals Achieved</i>', false);
        $response->assertSee('<b>3</b> <i>Action Items Achieved</i>', false);
        $response->assertSee('<b>10</b> <i>To-Do Items Completed</i>', false);
        $response->assertSee('<b>4</b> <i>Journal Entries</i>', false);
        $response->assertSee($now->format('F') . ' ' . 'Totals');
        $response->assertSee($now->format('n/j/y'));

        // Check day view
        $response = $this->actingAs($user)->get(route('journal.view.day', ['date' => $now->format('Y-m-d')]));
        $response->assertOk();
        foreach($habits as $habit)
        {
            $response->assertSee("<b>$habit->name:</b> <i>1 Times </i>", false);
        }
        $response->assertSee('<b>Affirmations Read:</b> <i>9 Times</i>', false);
        $response->assertSee("<b>Achieved Goal:</b> <i>$goal->name</i>", false);
        foreach($action_items as $action_item)
        {
            $response->assertSee("<b>Achieved Action Item:</b> <i>$action_item->name</i>", false);
        }
        foreach($to_dos as $to_do)
        {
            $response->assertSee("<b>Completed To-Do:</b> <i>$to_do->title</i>", false);
        }
        foreach($journal_entries as $journal_entry)
        {
            $response->assertSee("<b>$journal_entry->title: </b>", false);
            $response->assertSee($journal_entry->uuid);
            $response->assertSee($journal_entry->body);
        }
    }

    /**
     * Testing that the entry creation form and 
     * store entry routes work
     *
     * @return void
     * @test
     */
    public function testCreateEntry()
    {
        // Create test user
        $timezone = 'America/Denver';
        $user = User::factory()->create();
        $user->timezone = $timezone;
        $this->assertTrue($user->save());
        $user->refresh();

        // Get current timestamp
        $now = Carbon::now();
        $now->setTimezone($timezone);

        // Test create form
        $response = $this->actingAs($user)->get(route('journal.create.entry'));
        $response->assertOk();
        $response->assertSee('Add New Entry');

        // Actually submit an entry
        $response = $this->actingAs($user)->post(route('journal.store.entry'), [
            '_token' => csrf_token(),
            'title' => 'Fucking Unit Tests All Damn Day',
            'mood-3' => 'checked',
            'body' => 'asdf asldkfj alsd',
            'category' => 'no-category',
        ]);
        
        $journal_entry = JournalEntry::where('user_id', $user->id)->first();
        $this->assertEquals('Fucking Unit Tests All Damn Day', $journal_entry->title);
        $this->assertEquals('asdf asldkfj alsd', $journal_entry->body);
        $this->assertEquals(3, $journal_entry->mood_id);
        $response->assertRedirect(route('journal.view.entry', ['journal_entry' => $journal_entry->uuid]));
        
        // Check entry view
        $response = $this->actingAs($user)->get(route('journal.view.entry', ['journal_entry' => $journal_entry->uuid]));
        $response->assertOk();
        $response->assertSee($journal_entry->title);
        $response->assertSee($journal_entry->body);
    }

    /**
     * Testing that the entry update form and 
     * update entry routes work
     *
     * @return void
     * @test
     */
    public function testUpdateEntry()
    {
        // Create test user
        $timezone = 'America/Denver';
        $user = User::factory()->create();
        $user->timezone = $timezone;
        $this->assertTrue($user->save());
        $user->refresh();

        // Get current timestamp
        $now = Carbon::now();
        $now->setTimezone($timezone);

        // Create a test entry to update
        $journal_entry = JournalEntry::factory()->create([
            'user_id' => $user->id,
            'created_at' => $now->toDateTimeString(),
        ]);

        // Test create form
        $response = $this->actingAs($user)->get(route('journal.edit.entry', ['journal_entry' => $journal_entry->uuid]));
        $response->assertOk();
        $response->assertSee('Edit Entry');
        $response->assertSee($journal_entry->title);
        $response->assertSee($journal_entry->body);

        // Actually submit an entry
        $response = $this->actingAs($user)->post(route('journal.update.entry', ['journal_entry' => $journal_entry->uuid]), [
            '_token' => csrf_token(),
            'title' => 'Fucking Unit Tests All Damn Day',
            'mood-3' => 'checked',
            'body' => 'asdf asldkfj alsd',
            'category' => 'no-category',
        ]);
        $response->assertRedirect(route('journal.view.entry', ['journal_entry' => $journal_entry->uuid]));
        $journal_entry->refresh();

        $this->assertEquals('Fucking Unit Tests All Damn Day', $journal_entry->title);
        $this->assertEquals('asdf asldkfj alsd', $journal_entry->body);
        $this->assertEquals(3, $journal_entry->mood_id);
        
        // Check entry view
        $response = $this->actingAs($user)->get(route('journal.view.entry', ['journal_entry' => $journal_entry->uuid]));
        $response->assertOk();
        $response->assertSee($journal_entry->title);
        $response->assertSee($journal_entry->body);
    }

    /**
     * Testing that the route for deleting
     * entries works
     *
     * @return void
     * @test
     */
    public function testDestroyEntry()
    {
        // Create test user
        $user = User::factory()->create();

        // Create a test entry to update
        $journal_entry = JournalEntry::factory()->create([
            'user_id' => $user->id,
        ]);

        // Check we can view entry
        $response = $this->actingAs($user)->get(route('journal.view.entry', ['journal_entry' => $journal_entry->uuid]));
        $response->assertOk();

        // Delete entry
        $response = $this->actingAs($user)->post(route('journal.destroy.entry', ['journal_entry' => $journal_entry->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Check entry is gone
        $response = $this->actingAs($user)->get(route('journal.view.entry', ['journal_entry' => $journal_entry->uuid]));
        $response->assertStatus(404);
    }

    /**
     * Testing that the route for creating categories
     * works
     *
     * @return void
     * @test
     */
    public function testCreateCategory()
    {
        // Create test user
        $user = User::factory()->create();

        // Create entry
        $response = $this->actingAs($user)->post(route('journal.store.category'), [
            '_token' => csrf_token(),
            'name' => 'Test Category',
        ]);

        // Check we can view entry
        $response = $this->actingAs($user)->get(route('journal.edit.categories'));
        $response->assertOk();
        $response->assertSee('Test Category');

    }

    /**
     * Testing that the route for destroying categories works
     *
     * @return void
     * @test
     */
    public function testDestroyCategory()
    {
        // Create test user
        $user = User::factory()->create();

        // Create a test category to delete
        $journal_category = JournalCategory::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Category',
        ]);

        // Check we can view entry
        $response = $this->actingAs($user)->get(route('journal.edit.categories'));
        $response->assertOk();
        $response->assertSee('Test Category');

        // Delete entry
        $response = $this->actingAs($user)->post(route('journal.destroy.category', ['category' => $journal_category->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Check entry is gone
        $journal_category->refresh();
        $this->assertNotNull($journal_category->deleted_at);
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
        $response = $this->actingAs($user)->followingRedirects()->get(route('journal.colors'));
        $response->assertOk();
        $response->assertSee('Positive Mood');
        $response->assertSee('Neutral Mood');
        $response->assertSee('Negative Mood');
        $response->assertSee('Default Mood');
    }

    /**
     * Testing that the entry search works
     *
     * @return void
     * @test
     */
    public function testEntrySearch()
    {
        // Create test user
        $timezone = 'America/Denver';
        $user = User::factory()->create();
        $user->timezone = $timezone;
        $this->assertTrue($user->save());
        $user->refresh();

        // Get current timestamp
        $now = Carbon::now();
        $now->setTimezone($timezone);

        // Create a test entry to update
        $journal_entry = JournalEntry::factory()->create([
            'user_id' => $user->id,
            'created_at' => $now->toDateTimeString(),
            'body' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        Lorem Ipsum has been the industrys standard dummy text ever since the 1500s,
                        when an unknown printer took a galley of fuck you type and scrambled it to make a type specimen book.
                        It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.
                        It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages,
                        and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'
        ]);

        // Call search route
        $response = $this->actingAs($user)->post(route('journal.search'), [
            '_token' => csrf_token(),
            'keywords' => 'fuck you',
            'start-date' => $now->format('Y-m-d'),
            'end-date' => $now->format('Y-m-d'),
        ]);
        $response->assertSee($journal_entry->title);
    }
}
