<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

// Models
use App\Models\User\User;

class AuthTest extends TestCase
{
    /**
     * Test that unauthorized users get directed to the about page
     *
     * @return void
     * @test
     */
    public function testRedirectRootUnauthorized()
    {
        // Call root and assert redirect to about
        $response = $this->get('/');
        $response->assertRedirect('/about');
    }

    /**
     * Test that users with unverified emails get redirected
     * to the verify email route
     *
     * @return void
     * @test
     */
    public function testUnverifiedEmailRedirect()
    {
        // Make user
        $user = User::factory()->make();

        // Unverify user email
        $user->email_verified_at = null;
        $this->assertTrue($user->save());

        // Call root and assert redirect to home
        $response = $this->actingAs($user)->get('/');
        $response->assertRedirect('/todo');

        // Test email verification redirect
        $response = $this->actingAs($user)->get('/todo');
        $response->assertRedirect('/email/verify');
    }

    /**
     * Test that users with verified emails get redirected
     * to the home route
     *
     * @return void
     * @test
     */
    public function testVerifiedEmailRedirect()
    {
        // Make user
        $user = User::factory()->make();
        
        // Verify user email
        $user->email_verified_at = Carbon::now()->toDateTimeString();
        $this->assertTrue($user->save());

        // Test logged in redirect
        $response = $this->actingAs($user)->get('/');
        $response->assertRedirect('/todo');
    }

    /**
     * Test deleted users will delete
     *
     * @return void
     * @test
     */
    public function testDeleteUsers()
    {
        // Make user
        $user = User::factory()->create();

        // Verify user session destroyed on user delete
        $this->assertTrue($user->delete());
        $this->assertTrue(!is_null($user->deleted_at));
    }
}
