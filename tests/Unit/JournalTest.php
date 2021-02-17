<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;


// Models
use App\Models\Journal\JournalEntry;
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

        // Generate a goal and grab the UUID for testing
        $fake_journal_entry = JournalEntry::factory()->create();
        $journal_entry_uuid = $fake_journal_entry->uuid;
        $this->assertTrue($fake_journal_entry->delete());


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
    }
}
