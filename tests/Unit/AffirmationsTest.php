<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// Models
use App\Models\Affirmations\Affirmations;
use App\Models\Affirmations\AffirmationsReadLog;
use App\Models\User\User;

class AffirmationsTest extends TestCase
{
    /**
     * Tests affirmations UUID routes with fake UUIDs for 404 errors
     *
     * @return void
     * @test
     */
    public function testFakeUUIDs()
    {
        // Create test user
        $user = User::factory()->create();

        // Generate fake affirmations for user
        Affirmations::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Generate a todo and grab the UUID for testing
        $fake_affirmation = Affirmations::factory()->create();
        $uuid = $fake_affirmation->uuid;
        $this->assertTrue($fake_affirmation->delete());

        // Test show route
        $response = $this->actingAs($user)->get(route('affirmations.show', ['affirmation' => $uuid]));
        $response->assertStatus(404);

        // Test edit route
        $response = $this->actingAs($user)->get(route('affirmations.edit', ['affirmation' => $uuid]));
        $response->assertStatus(404);

        // Test update route
        $response = $this->actingAs($user)->post(route('affirmations.update', ['affirmation' => $uuid]));
        $response->assertStatus(404);

        // Test destroy route
        $response = $this->actingAs($user)->post(route('affirmations.destroy', ['affirmation' => $uuid]));
        $response->assertStatus(404);
    }

    /**
     * Tests affirmation UUID routes with a UUID that doesn't belong
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

        // Generate fake affirmations for user
        Affirmations::factory(rand(5, 15))->create([
            'user_id' => $forbidden_user->id,
        ]);

        // Get a forbidden UUID
        $uuid = Affirmations::where('user_id', $forbidden_user->id)->first()->uuid;

        // Test show route
        $response = $this->actingAs($test_user)->get(route('affirmations.show', ['affirmation' => $uuid]));
        $response->assertStatus(403);

        // Test edit route
        $response = $this->actingAs($test_user)->get(route('affirmations.edit', ['affirmation' => $uuid]));
        $response->assertStatus(403);

        // Test update route
        $response = $this->actingAs($test_user)->post(route('affirmations.update', ['affirmation' => $uuid]));
        $response->assertStatus(403);

        // Test destroy route
        $response = $this->actingAs($test_user)->post(route('affirmations.destroy', ['affirmation' => $uuid]));
        $response->assertStatus(403);
    }

    /**
     * Tests that the affirmations index page is redirecting properly
     *
     * @return void
     * @test
     */
    public function testIndex()
    {
        // Create test user
        $user = User::factory()->create();

        // Call index page and check for redirect to affirmations add page
        $response = $this->actingAs($user)->get(route('affirmations'));
        $response->assertRedirect('/affirmations/create');

        // Generate fake affirmations for user
        Affirmations::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Refresh model and get first uuid
        $user->refresh();
        $uuid = $user->affirmations->first()->uuid;

        // Call index page and check for redirect to affirmations 
        $response = $this->actingAs($user)->get(route('affirmations'));
        $response->assertRedirect("/affirmations/show/$uuid");
    }

    /**
     * Tests that the affirmations show properly, settings test
     * handles checking for the good job read page
     *
     * @return void
     * @test
     */
    public function testShow()
    {
        // Create test user
        $user = User::factory()->create();

        // Generate fake affirmations for user
        Affirmations::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Iterate through each affirmation and verify it shows up properly
        foreach($user->affirmations as $affirmation)
        {
            $response = $this->actingAs($user)->get(route('affirmations.show', ['affirmation' => $affirmation->uuid]));
            $response->assertStatus(200);
            $response->assertSee($affirmation->value);
        }
    }

    /**
     * Tests finishing reading the affirmations will log the completion
     * and redirect if the list was just clicked through
     * @return void
     * @test
     */
    public function testRead()
    {
        // Create test user and generate fake affirmations
        $user = User::factory()->create();
        Affirmations::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Refresh model
        $user->refresh();

        // Check that there is no affirmation logs
        $this->assertTrue($user->affirmationsReadLog->count() == 0);

        // Go though all the affirmations properly
        foreach($user->affirmations as $affirmation)
        {
            $response = $this->actingAs($user)->get(route('affirmations.show', ['affirmation' => $affirmation->uuid]));
            $response->assertStatus(200);
            usleep(100);
        }

        // Verify that calling the read function returns the read page redirect
        sleep(2);
        $response = $this->actingAs($user)->post(route('affirmations.read.check'), [
            '_token' => csrf_token(),
        ]);
        $response->assertRedirect('/affirmations/read');

        // Refresh model
        $user->refresh();

        // And that a read log was added
        $this->assertTrue($user->affirmationsReadLog->count() == 1);

        // Verify that calling the read function returns the affirmations redirect
        $response = $this->actingAs($user)->post(route('affirmations.read.check'), [
            '_token' => csrf_token(),
        ]);
        $response->assertRedirect("/affirmations");
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
        $response = $this->actingAs($user)->get(route('affirmations.create'));
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2> Add  Affirmation</h2>', false);
        $response->assertSee('action="' . route('affirmations.store') . '"', false);
        $response->assertSee('<textarea name="affirmation" rows="4" maxlength="255" placeholder="Your positive statement goes here!" required></textarea>', false);
        $response->assertSee('<button class="cancel" type="button">Cancel</button>', false);
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

        // Generate fake affirmations for user
        Affirmations::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Refresh model and get first affirmation
        $user->refresh();
        $affirmation = $user->affirmations->first();

        // Get response
        $response = $this->actingAs($user)->get(route('affirmations.edit', ['affirmation' => $affirmation->uuid]));
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2>Edit  Affirmation</h2>', false);
        $response->assertSee('action="' . route('affirmations.update', ['affirmation' => $affirmation->uuid]) . '"', false);
        $response->assertSee('<textarea name="affirmation" rows="4" maxlength="255" placeholder="Your positive statement goes here!" required>' . $affirmation->value . '</textarea>', false);
        $response->assertSee('<button class="cancel" type="button">Cancel</button>', false);
        $response->assertSee('<button class="submit" type="submit">Submit</button>', false);
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

        // Send data to create a new affirmation
        $response = $this->actingAs($user)->post(route('affirmations.store'), [
            '_token' => csrf_token(),
            'affirmation' => 'This affirmation is testing the store route.'
        ]);

        // Verify redirected back to to do list properly
        $response->assertRedirect('/affirmations');

        // Verify it stored
        $this->assertEquals($user->affirmations->first()->value, 'This affirmation is testing the store route.');
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

        // Give it an affirmation
        $affirmation = Affirmations::factory()->create([
            'user_id' => $user->id,
        ]);

        // Send data to update affirmation
        $response = $this->actingAs($user)->post(route('affirmations.update', ['affirmation' => $affirmation->uuid]), [
            '_token' => csrf_token(),
            'affirmation' => 'This affirmation is testing the update route.'
        ]);

        // Verify redirected back to the affirmation properly
        $response->assertRedirect("affirmations/show/$affirmation->uuid");

        // Refresh model
        $affirmation->refresh();

        $this->assertEquals($affirmation->value, 'This affirmation is testing the update route.');
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
        $affirmation = Affirmations::factory()->create([
            'user_id' => $user->id,
        ]);

        // Send data to delete to-do item
        $response = $this->actingAs($user)->post(route('affirmations.destroy', ['affirmation' => $affirmation->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Verify redirected back to affirmations properly
        $response->assertRedirect('/affirmations');

        // Verify user doesn't have any affirmations now
        $user->refresh();
        $this->assertTrue($user->affirmations->count() == 0);
    }
}
