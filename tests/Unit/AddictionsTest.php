<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// Constants
use App\Helpers\Constants\Addiction\DateFormat;
use App\Helpers\Constants\Addiction\Method;

// Models
use App\Models\Addictions\Addiction;
use App\Models\Addictions\AddictionRelapse;
use App\Models\User\User;

class AddictionsTest extends TestCase
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

        // Generate fake addictions items for user
        Addiction::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Generate fake addiction grab the UUID for testing
        $fake_addiction = Addiction::factory()->create();

        // Grab a fake milestone
        $fake_milestone = $fake_addiction->milestones->first();
        $milestone_uuid = $fake_milestone->uuid;
        $this->assertTrue($fake_milestone->delete());

        // Generate a fake relapse
        // $fake_relapse = AddictionRelapse::factory()->create([
        //     'addiction_id' => $fake_addiction->id,
        // ]);
        // $relapse_uuid = $fake_relapse->uuid;
        // $this->assertTrue($fake_relapse->delete());

        // Set fake addiction uuid and delete
        $uuid = $fake_addiction->uuid;
        $this->assertTrue($fake_addiction->delete());

        // Test addiction uuid routes
        $response = $this->actingAs($user)->get(route('addiction.details', ['addiction' => $uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->get(route('addiction.edit', ['addiction' => $uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->post(route('addiction.update', ['addiction' => $uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->get(route('addiction.milestone.create', ['addiction' => $uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->post(route('addiction.milestone.store', ['addiction' => $uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->get(route('addiction.relapse.timeline', ['addiction' => $uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->get(route('addiction.relapse.create', ['addiction' => $uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->post(route('addiction.relapse.store', ['addiction' => $uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->post(route('addiction.usage.store', ['addiction' => $uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->post(route('addiction.destroy', ['addiction' => $uuid]));
        $response->assertStatus(404);

        // Test milestone routes
        $response = $this->actingAs($user)->post(route('addiction.milestone.destroy', ['milestone' => $milestone_uuid]));
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
        // Create test user
        $user = User::factory()->create();

        // Create forbidden user
        $forbidden_user = User::factory()->create();

        // Generate fake addictions for user
        Addiction::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Generate a addiction and grab the UUID for testing
        $forbidden_addiction = Addiction::factory()->create([
            'user_id' => $forbidden_user->id,
        ]);
        $uuid = $forbidden_addiction->uuid;
        $milestone_uuid = $forbidden_addiction->milestones->first();

        // Test addiction uuid routes
        $response = $this->actingAs($user)->get(route('addiction.details', ['addiction' => $uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($user)->get(route('addiction.edit', ['addiction' => $uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($user)->post(route('addiction.update', ['addiction' => $uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($user)->get(route('addiction.milestone.create', ['addiction' => $uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($user)->post(route('addiction.milestone.store', ['addiction' => $uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($user)->get(route('addiction.relapse.timeline', ['addiction' => $uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($user)->get(route('addiction.relapse.create', ['addiction' => $uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($user)->post(route('addiction.relapse.store', ['addiction' => $uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($user)->post(route('addiction.usage.store', ['addiction' => $uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($user)->post(route('addiction.destroy', ['addiction' => $uuid]));
        $response->assertStatus(403);

        // Test milestone routes
        $response = $this->actingAs($user)->post(route('addiction.milestone.destroy', ['milestone' => $milestone_uuid]));
        $response->assertStatus(403);
    }

    /**
     * Tests hitting the index page to see all addictions
     *
     * @return void
     * @test
     */
    public function testIndex()
    {
        // Create test user
        $user = User::factory()->create();

        // Generate fake affirmations for user
        $addictions = Addiction::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('addictions'));
        $response->assertStatus(200);

        foreach ($addictions as $addiction) {
            $response->assertSee($addiction->name);
        }
    }

    /**
     * Tests storing a new addiction
     *
     * @return void
     * @test
     */
    public function testStoreAddiction()
    {
        // Create test user
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('addiction.create'));
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2> Create New Addiction </h2>', false);
        $response->assertSee('action="' . route('addiction.store') . '"', false);
        $response->assertSee('<input type="text" name="name" placeholder="Addiction Name" maxlength="255"', false);
        $response->assertSee('<span class="start-on">', false);
        $response->assertSee('<select id="method-selector" name="method" data-moderation="2" required>', false);
        $response->assertSee('<span id="moderation-span" class="moderation hide">', false);
        $response->assertSee('<textarea name="details" placeholder="Why do you want to break this addiction, and what negative effects is it having on your life? What will you do instead when you feel tempted to cave, and what positives will you be rewarded', false);
        $response->assertSee('<button class="submit" type="submit">Submit</button>', false);

        // Send data to create a new to-do item
        $response = $this->actingAs($user)->post(route('addiction.store'), [
            '_token' => csrf_token(),
            'start-date' => '2021-11-17',
            'name' => 'Test Addiction',
            'method' => Method::ABSTINENCE,
            'details' => 'This addiction is testing the store route.',
        ]);

        // Get that to do item
        $addiction = Addiction::where('user_id', $user->id)->first();
        $this->assertNotNull($addiction);
        $this->assertEquals($addiction->name, 'Test Addiction');
        $this->assertEquals($addiction->details, 'This addiction is testing the store route.');

        // Verify redirected back to to do list properly
        $response->assertRedirect('/addictions/details/' . $addiction->uuid);
    }

     /**
     * Tests that the addiction update route works
     *
     * @return void
     * @test
     */
    public function testUpdate()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it an addiction (sorry buddy)
        $addiction = Addiction::factory()->create([
            'user_id' => $user->id,
        ]);

        // Get response
        $response = $this->actingAs($user)->get(route('addiction.edit', ['addiction' => $addiction->uuid]));
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2> Edit Addiction </h2>', false);
        $response->assertSee('action="' . route('addiction.update', ['addiction' => $addiction->uuid]) . '"', false);
        $response->assertSee('value="' . $addiction->name . '"', false);
        $response->assertSee('>' . $addiction->details . '</textarea>', false);
        $response->assertSee('<button class="submit" type="submit">Submit</button>', false);

        // Send data to create a new to-do item
        $response = $this->actingAs($user)->post(route('addiction.update', ['addiction' => $addiction->uuid]), [
            '_token' => csrf_token(),
            'start-date' => '2021-11-17',
            'name' => 'Test Addiction',
            'method' => Method::ABSTINENCE,
            'details' => 'This addiction is testing the store route.',
        ]);

        // Verify redirected back to to do list properly
        $response->assertRedirect('/addictions/details/' . $addiction->uuid);

        // Get that to do item
        $addiction = Addiction::where('user_id', $user->id)->first();

        $this->assertEquals($addiction->name, 'Test Addiction');
        $this->assertEquals($addiction->details, 'This addiction is testing the store route.');
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

        // Give it an addiction
        $addiction = Addiction::factory()->create([
            'user_id' => $user->id,
        ]);

        // Send data to delete addiction
        $response = $this->actingAs($user)->post(route('addiction.destroy', ['addiction' => $addiction->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Verify redirected back to the addictions list properly
        $response->assertRedirect('/addictions');

        // Get that addiction
        $addiction = Addiction::where('user_id', $user->id)->first();

        // Verify it's not returned now
        $this->assertNull($addiction);
    }

    /**
     * Tests that details page works
     *
     * @return void
     * @test
     */
    public function testDetails()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it an addiction
        $addiction = Addiction::factory()->create([
            'user_id' => $user->id,
        ]);

        // Get the addiction detail page
        $response = $this->actingAs($user)->get(route('addiction.details', ['addiction' => $addiction->uuid]));
        $response->assertStatus(200);

        // Verify some stuff on the details page
        $response->assertSee(config('addictions.methods')[$addiction->method_id]['name']);
        $response->assertSee($addiction->name);
        $response->assertSee($addiction->details);
    }

    /**
     * Tests the milestone list
     *
     * @return void
     * @test
     */
    public function testMilestoneList()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it an addiction
        $addiction = Addiction::factory()->create([
            'user_id' => $user->id,
        ]);

        // Get the milestone list
        $response = $this->actingAs($user)->get(route('addiction.milestones', ['addiction' => $addiction->uuid]));
        $response->assertStatus(200);

        // Verify I see milestones
        foreach ($addiction->milestones as $milestone) {
            $response->assertSee($milestone->name);
        }
    }

    /**
     * Tests adding a milestone to an addiction
     *
     * @return void
     * @test
     */
    public function testAddMilestone()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it an addiction
        $addiction = Addiction::factory()->create([
            'user_id' => $user->id,
        ]);

        // Test creating a milestone
        $response = $this->actingAs($user)->post(route('addiction.milestone.store', ['addiction' => $addiction->uuid]), [
            '_token' => csrf_token(),
            'name' => 'Test Milestone',
            'milestone-amount' => '1',
            'milestone-date-format' => DateFormat::YEAR,
            'reward' => 'Deez nutz',
        ]);
        $response->assertRedirect("addictions/milestones/$addiction->uuid");

        // Check for it on the milestone list
        $response = $this->actingAs($user)->get(route('addiction.milestones', ['addiction' => $addiction->uuid]));
        $response->assertStatus(200);
        $response->assertSee('Test Milestone');
        $response->assertSee('Deez nutz');
    }

    /**
     * Tests deleting an addiction
     *
     * @return void
     * @test
     */
    public function testDestroyMilestone()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it an addiction
        $addiction = Addiction::factory()->create([
            'user_id' => $user->id,
        ]);

        // Get milestone
        $milestone = $addiction->milestones->first();

        // Test creating a milestone
        $response = $this->actingAs($user)->post(route('addiction.milestone.destroy', ['milestone' => $milestone->uuid]), [
            '_token' => csrf_token(),
        ]);
        $response->assertRedirect("addictions/milestones/$addiction->uuid");

        // Check for it on the milestone list
        $addiction->refresh();
        $this->assertTrue($milestone->uuid != $addiction->milestones->first()->uuid);
    }

    /**
     * Tests storing usage and relapse
     *
     * @return void
     * @test
     */
    public function testRelapse()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it an addiction
        $addiction = Addiction::factory()->create([
            'user_id' => $user->id,
            'method_id' => Method::MODERATION,
            'moderated_amount' => 1,
            'moderated_period' => 1,
            'moderated_date_format_id' => DateFormat::YEAR,
        ]);

        // Check the details page for the mark usage option
        $response = $this->actingAs($user)->get(route('addiction.details', ['addiction' => $addiction->uuid]));
        $response->assertStatus(200);
        $response->assertSee('Mark Usage');

        // Mark usage
        $response = $this->actingAs($user)->post(route('addiction.usage.store', ['addiction' => $addiction->uuid]), [
            '_token' => csrf_token(),
        ]);
        $response->assertRedirect("addictions/details/$addiction->uuid");

        // Check for mark relapse option
        $response = $this->actingAs($user)->get(route('addiction.details', ['addiction' => $addiction->uuid]));
        $response->assertStatus(200);
        $response->assertSee('Mark Relapse');

        // Check relapse form
        $response = $this->actingAs($user)->get(route('addiction.relapse.create', ['addiction' => $addiction->uuid]));
        $response->assertStatus(200);
        $response->assertSee('Mark Relapse');
        $response->assertSee('action="' . route('addiction.relapse.store', ['addiction' => $addiction->uuid]) . '"', false);
        $response->assertSee('Remember that relapses do not mean you\'ve failed, they are part of the recovery process. You got this!', false);

        // Store a relapse
        $response = $response = $this->actingAs($user)->post(route('addiction.relapse.store', ['addiction' => $addiction->uuid]), [
            '_token' => csrf_token(),
            'notes' => 'Testing testing 1 2 3...',
        ]);
        $response->assertRedirect("addictions/details/$addiction->uuid");

        // Check the details
        $response = $this->actingAs($user)->get(route('addiction.details', ['addiction' => $addiction->uuid]));
        $response->assertStatus(200);
        $response->assertSee('View Relapses');

        // Call the relapse page
        $response = $this->actingAs($user)->get(route('addiction.relapse.timeline', ['addiction' => $addiction->uuid]));
        $response->assertStatus(200);
        $response->assertSee('Testing testing 1 2 3...');
    }
}
