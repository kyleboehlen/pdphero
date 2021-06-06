<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use DB;

// Models
use App\Models\Feature\Feature;
use App\Models\Feature\FeatureVote;
use App\Models\User\User;

class FeatureVoteTest extends TestCase
{
    /**
     * Testing that the feature vote route works
     *
     * @return void
     * @test
     */
    public function testFeatureVote()
    {
        // Create a feature to test with
        $feature = Feature::factory()->create();
        $cur_score = $feature->score;

        // Create test user
        $user = User::factory()->create();

        // Make user a black label user
        DB::table('subscriptions')->insert([
            'user_id' => $user->id,
            'name' => config('membership.black_label.slug'),
            'stripe_id' => '69420',
            'stripe_status' => 'active',
            'stripe_plan' => '69420',
        ]);

        // Check default
        $response = $this->actingAs($user)->get(route('feature.details', ['feature' => $feature->uuid]));
        $response->assertOk();
        $response->assertSee('I Don\'t Care About This Feature');

        // Cast don't want vote
        $response = $this->actingAs($user)->post(route('feature.vote', ['feature' => $feature->uuid]), [
            '_token' => csrf_token(),
            'dont-want' => 'checked',
        ]);

        // Check change
        $response = $this->actingAs($user)->get(route('feature.details', ['feature' => $feature->uuid]));
        $response->assertOk();
        $response->assertSee('I Don\'t Want This Feature');
        $cur_score--;
        $feature->refresh();
        $this->assertEquals($cur_score, $feature->score);

        // Cast want vote
        $response = $this->actingAs($user)->post(route('feature.vote', ['feature' => $feature->uuid]), [
            '_token' => csrf_token(),
            'want' => 'checked',
        ]);

        // Check change
        $response = $this->actingAs($user)->get(route('feature.details', ['feature' => $feature->uuid]));
        $response->assertOk();
        $response->assertSee('I Want This Feature');
        $cur_score += 2;
        $feature->refresh();
        $this->assertEquals($cur_score, $feature->score);

        // Delete feature
        $this->assertTrue($feature->delete());
    }
}
