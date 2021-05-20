<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// Models
use App\Models\Home\Home;
use App\Models\User\User;

class HomeTest extends TestCase
{
    /**
     * Tests the home page
     *
     * @return void
     * @test
     */
    public function testIndex()
    {
        // Create test user
        $user = User::factory()->create();

        // (ET) Call home... wait that's not even the line, it's phone home. God dammit.
        $response = $this->actingAs($user)->get(route('home'));
        $response->assertStatus(200);

        foreach(config('home') as $home)
        {
            $response->assertSee($home['name']);
            $response->assertSee($home['desc']);
            $response->assertSee($home['img']);
        }
    }

    /**
     * Tests the hide/show routes
     *
     * @return void
     * @test
     */
    public function testHideShow()
    {
        // Create test user
        $user = User::factory()->create();

        // Get a home id
        $home_id = Home::all()->random()->id;

        // Hide it
        $response = $this->actingAs($user)->followingRedirects()->post(route('home.hide', ['home' => $home_id]), [
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);

        // Refresh model and get hide array
        $user->refresh();
        $hide_array = $user->hideHomeArray();

        $this->assertTrue(in_array($home_id, $hide_array));

        // Show it
        $response = $this->actingAs($user)->followingRedirects()->post(route('home.show', ['home' => $home_id]), [
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);

        // Refresh model and get hide array
        $user->refresh();
        $hide_array = $user->hideHomeArray();

        $this->assertTrue(!in_array($home_id, $hide_array));
    }
}
