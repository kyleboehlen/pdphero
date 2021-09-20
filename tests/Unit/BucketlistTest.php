<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// Models
use App\Models\Bucketlist\BucketlistCategory;
use App\Models\Bucketlist\BucketlistItem;
use App\Models\User\User;

class BucketlistTest extends TestCase
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

        // Generate fake bucketlist items for user
        BucketlistItem::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Generate a bucketlist item and grab the UUID for testing
        $fake_item = BucketlistItem::factory()->create();
        $uuid = $fake_item->uuid;
        $this->assertTrue($fake_item->delete());

        // Test edit route
        $response = $this->actingAs($user)->get(route('bucketlist.edit', ['bucketlist_item' => $uuid]));
        $response->assertStatus(404);

        // Test view details route
        $response = $this->actingAs($user)->get(route('bucketlist.view.details', ['bucketlist_item' => $uuid]));
        $response->assertStatus(404);

        // Test update route
        $response = $this->actingAs($user)->post(route('bucketlist.update', ['bucketlist_item' => $uuid]));
        $response->assertStatus(404);

        // Test destroy route
        $response = $this->actingAs($user)->post(route('bucketlist.destroy', ['bucketlist_item' => $uuid]));
        $response->assertStatus(404);

        // Test toggle completed/incomplete route
        $response = $this->actingAs($user)->post(route('bucketlist.mark-completed', ['bucketlist_item' => $uuid]));
        $response->assertStatus(404);
        $response = $this->actingAs($user)->post(route('bucketlist.mark-incomplete', ['bucketlist_item' => $uuid]));
        $response->assertStatus(404);

        // Generate a fake bucketlist category and test uuid
        $fake_category = BucketlistCategory::factory()->create();
        $uuid = $fake_category->uuid;
        $this->assertTrue($fake_category->delete());

        // Test destroy route
        $response = $this->actingAs($user)->post(route('bucketlist.destroy.category', ['category' => $uuid]));
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

        // Generate fake bucketlist items for user
        BucketlistItem::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Generate a bucketlist item and grab the UUID for testing
        $forbidden_item = BucketlistItem::factory()->create([
            'user_id' => $forbidden_user->id,
        ]);
        $uuid = $forbidden_item->uuid;

        // Test edit route
        $response = $this->actingAs($user)->get(route('bucketlist.edit', ['bucketlist_item' => $uuid]));
        $response->assertStatus(403);

        // Test view details route
        $response = $this->actingAs($user)->get(route('bucketlist.view.details', ['bucketlist_item' => $uuid]));
        $response->assertStatus(403);

        // Test update route
        $response = $this->actingAs($user)->post(route('bucketlist.update', ['bucketlist_item' => $uuid]));
        $response->assertStatus(403);

        // Test destroy route
        $response = $this->actingAs($user)->post(route('bucketlist.destroy', ['bucketlist_item' => $uuid]));
        $response->assertStatus(403);

        // Test toggle completed/incomplete route
        $response = $this->actingAs($user)->post(route('bucketlist.mark-completed', ['bucketlist_item' => $uuid]));
        $response->assertStatus(403);
        $response = $this->actingAs($user)->post(route('bucketlist.mark-incomplete', ['bucketlist_item' => $uuid]));
        $response->assertStatus(403);

        // Generate a fake bucketlist category and test uuid
        $forbidden_category = BucketlistCategory::factory()->create([
            'user_id' => $forbidden_user->id,
        ]);
        $uuid = $forbidden_category->uuid;

        // Test destroy route
        $response = $this->actingAs($user)->post(route('bucketlist.destroy.category', ['category' => $uuid]));
        $response->assertStatus(403);
    }

    /**
     * Tests that the complete/incomplete bucketlists
     *
     * @return void
     * @test
     */
    public function testBucketlist()
    {
        // Create test user
        $user = User::factory()->create();

        // Give it bucketlist items
        BucketlistItem::factory(rand(15, 60))->create([
            'user_id' => $user->id,
        ]);

        // Get completed bucketlist items
        $completed_items = BucketlistItem::where('user_id', $user->id)->where('achieved', 1)->get();

        // Verify completed timeline has all the items
        $response = $this->actingAs($user)->get(route('bucketlist.view.completed'));
        $response->assertStatus(200);
        foreach($completed_items as $item)
        {
            $response->assertSee($item->name);
        }

        // Get incomplete bucketlist items
        $incomplete_items = BucketlistItem::where('user_id', $user->id)->where('achieved', 0)->get();

        // Verify list has each of the to do items
        $response = $this->actingAs($user)->get(route('bucketlist'));
        $response->assertStatus(200);
        foreach($incomplete_items as $item)
        {
            $response->assertSee($item->name);
        }
    }

    /**
     * Tests the complete/incomplete bucketlist item routes
     *
     * @return void
     * @test
     */
    public function testCompleteBucketlistItem()
    {
        // Create test user
        $user = User::factory()->create();

        // Create a completed item
        $bucketlist_item = BucketlistItem::factory()->create([
            'user_id' => $user->id,
            'achieved' => 1,
        ]);

        // Toggle it to incomplete
        $response = $this->actingAs($user)->post(route('bucketlist.mark-incomplete', ['bucketlist_item' => $bucketlist_item->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Verify it's incomplete
        $bucketlist_item->refresh();
        $this->assertFalse((bool) $bucketlist_item->achieved);

        // Toggle it to complete
        $response = $this->actingAs($user)->post(route('bucketlist.mark-completed', ['bucketlist_item' => $bucketlist_item->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Verify it's incomplete
        $bucketlist_item->refresh();
        $this->assertTrue((bool) $bucketlist_item->achieved);
    }

    /**
     * Tests creating, assigning, and displaying a bucketlist category
     *
     * @return void
     * @test
     */
    public function testCreateCategory()
    {
        // Create test user
        $user = User::factory()->create();

        // Create bucketlist items
        BucketlistItem::factory(2)->create([
            'user_id' => $user->id,
            'achieved' => false,
        ]);
        $bucketlist_item = BucketlistItem::where('user_id', $user->id)->first();

        // Call the edit categories route
        $response = $this->actingAs($user)->get(route('bucketlist.edit.categories'));
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2>Edit Categories</h2>', false);
        $response->assertSee('<input type="text" name="name" placeholder="Add a new category" maxlength="255" />', false);
        $response->assertSee('<button class="add" type="submit">Add</button>', false);

        // Create a category
        $response = $this->actingAs($user)->post(route('bucketlist.store.category'), [
            '_token' => csrf_token(),
            'name' => 'Test Category',
        ]);

        // Verify redirected back to the categories page properly
        $response->assertRedirect('/bucketlist/categories');

        // Verify it shows up on the edit categories page now
        $response = $this->actingAs($user)->get(route('bucketlist.edit.categories'));
        $response->assertStatus(200);
        $response->assertSee('Test Category');

        // Refresh use to get the category id
        $user->refresh();
        $category = $user->bucketlistCategories()->first();

        // Assign it to a bucketlist item
        $bucketlist_item->category_id = $category->id;
        $this->assertTrue($bucketlist_item->save());

        // Check the todo page to see if the category select shows up
        $response = $this->actingAs($user)->get(route('bucketlist'));
        $response->assertStatus(200);
        $response->assertSee($category->name);
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
        $category = BucketlistCategory::factory()->create([
            'user_id' => $user->id,
        ]);

        // Verify it has a category
        $this->assertEquals($user->bucketlistCategories()->get()->count(), 1);

        // Send data to delete to-do category
        $response = $this->actingAs($user)->post(route('bucketlist.destroy.category', ['category' => $category->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Verify redirected back to the manage todo categories page properly
        $response->assertRedirect('/bucketlist/categories');

        // Refresh user and get todo categories
        $user->refresh();
        $category = $user->bucketlistCategories()->first();

        // Verify it's not returned now
        $this->assertNull($category);
    }

    /**
     * Tests that the store route and create form works
     *
     * @return void
     * @test
     */
    public function testStore()
    {
        // Create test user
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('bucketlist.create'));
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2> Create New Item </h2>', false);
        $response->assertSee('action="' . route('bucketlist.store') . '"', false);
        $response->assertSee('<input type="text" name="name" placeholder="Bucketlist Item" maxlength="255"', false);
        $response->assertSee('<select name="category"', false);
        $response->assertSee('placeholder="Any extra details for your bucketlist item go here!"', false);

        // Send data to create a new to-do item
        $response = $this->actingAs($user)->post(route('bucketlist.store'), [
            '_token' => csrf_token(),
            'name' => 'Test Item',
            'category' => 'no-category',
            'details' => 'This item is testing the store route.',
        ]);

        // Verify redirected back to to do list properly
        $response->assertRedirect('/bucketlist');

        // Get that to do item
        $item = BucketlistItem::where('user_id', $user->id)->first();

        $this->assertEquals($item->name, 'Test Item');
        $this->assertEquals($item->notes, 'This item is testing the store route.');
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

        // Give it a bucketlist item
        $item = BucketlistItem::factory()->create([
            'user_id' => $user->id,
            'achieved' => false,
        ]);

        // Get response
        $response = $this->actingAs($user)->get(route('bucketlist.edit', ['bucketlist_item' => $item->uuid]));
        $response->assertStatus(200);

        // Check for various important parts of the form
        $response->assertSee('<h2> Edit Item </h2>', false);
        $response->assertSee('action="' . route('bucketlist.update', ['bucketlist_item' => $item->uuid]) . '"', false);
        $response->assertSee('value="' . $item->name . '"', false);
        $response->assertSee('>' . $item->notes . '</textarea>', false);
        $response->assertSee('<button class="submit" type="submit">Submit</button>', false);

        // Send data to create a new to-do item
        $response = $this->actingAs($user)->post(route('bucketlist.update', ['bucketlist_item' => $item->uuid]), [
            '_token' => csrf_token(),
            'name' => 'Test Item',
            'category' => 'no-category',
            'details' => 'This item is testing the update route.'
        ]);

        // Verify redirected back to to do list properly
        $response->assertRedirect('/bucketlist/view/' . $item->uuid);

        // Get that to do item
        $item = BucketlistItem::where('user_id', $user->id)->first();

        $this->assertEquals($item->name, 'Test Item');
        $this->assertEquals($item->notes, 'This item is testing the update route.');
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
        $item = BucketlistItem::factory()->create([
            'user_id' => $user->id,
        ]);

        // Send data to delete to-do item
        $response = $this->actingAs($user)->post(route('bucketlist.destroy', ['bucketlist_item' => $item->uuid]), [
            '_token' => csrf_token(),
        ]);

        // Verify redirected back to the bucketlist properly
        $response->assertRedirect('/bucketlist');

        // Get that to do item
        $item = BucketlistItem::where('user_id', $user->id)->first();

        // Verify it's not returned now
        $this->assertNull($item);
    }
}