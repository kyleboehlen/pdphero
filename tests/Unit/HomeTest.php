<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// Models
use App\Models\User\User;

class HomeTest extends TestCase
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
}
