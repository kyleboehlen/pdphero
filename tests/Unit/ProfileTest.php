<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

// Constants
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\User\User;
use App\Models\User\UsersSettings;

class ProfileTest extends TestCase
{
    use WithFaker;

    /**
     * Tests changing name
     *
     * @return void
     * @test
     */
    public function testName()
    {
        // Create test user
        $user = User::factory()->create();

        // Call profile and verify it can be seen
        $response = $this->actingAs($user)->get(route('profile'));
        $response->assertStatus(200);
        $response->assertSee($user->name);

        // Check the edit form shows it
        $response = $this->actingAs($user)->get(route('profile.edit.name'));
        $response->assertStatus(200);
        $response->assertSee($user->name);

        // Check the edit form actually works to update it
        $response = $this->actingAs($user)->post(route('profile.update.name'), [
            '_token' => csrf_token(),
            'name' => 'Fucky McFuckface', // with a *slight* scottish accent
        ]);

        // Verify redirected back properly
        $response->assertRedirect('/profile');

        // Refresh model
        $user->refresh();

        // And double check its showing up on the profile/edit pages
        $response = $this->actingAs($user)->get(route('profile'));
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response = $this->actingAs($user)->get(route('profile.edit.name'));
        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /**
     * Tests changing profile picture
     *
     * @return void
     * @test
     */
    public function testPicture()
    {
        // Create test user with no profile picture
        $user = User::factory()->create([
            'profile_picture' => null,
        ]);

        // Call profile and check it's default
        $response = $this->actingAs($user)->get(route('profile'));
        $response->assertStatus(200);
        $response->assertSee('src="' . asset('icons/profile-white.png') . '"', false);

        // Check the edit form actually works to update it
        $response = $this->actingAs($user)->post(route('profile.update.picture'), [
            '_token' => csrf_token(),
            'profile-picture' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        // Verify redirected back properly
        $response->assertRedirect('/profile');

        // Refresh model
        $user->refresh();

        // And double check its showing up on the profile page
        $response = $this->actingAs($user)->get(route('profile'));
        $response->assertStatus(200);
        $response->assertSee($user->profile_picture);
    }

    /**
     * Tests changing values
     *
     * @return void
     * @test
     */
    public function testValues()
    {
        // Create test user
        $user = User::factory()->create();

        // Call profile and verify it can be seen
        $response = $this->actingAs($user)->get(route('profile'));
        $response->assertStatus(200);
        foreach($user->values as $value)
        {
            $response->assertSee($value);
        }

        // Check the edit form shows it
        $response = $this->actingAs($user)->get(route('profile.edit.values'));
        $response->assertStatus(200);
        foreach($user->values as $value)
        {
            $response->assertSee($value);
        }

        // Add a value
        $response = $this->actingAs($user)->post(route('profile.update.values'), [
            '_token' => csrf_token(),
            'value' => $this->faker->word(),
        ]);

        // Verify redirected back properly
        $response->assertRedirect('/profile/edit/values');

        // Refresh model
        $user->refresh();

        // And double check its showing up on the profile/edit pages
        $profile_response = $this->actingAs($user)->get(route('profile'));
        $profile_response->assertStatus(200);
        $edit_response = $this->actingAs($user)->get(route('profile.edit.values'));
        $edit_response->assertStatus(200);
        foreach($user->values as $value)
        {
            $profile_response->assertSee($value);
            $edit_response->assertSee($value);
        }

        // Delete all of the values
        foreach($user->values as $value)
        {
            $response = $this->actingAs($user)->post(route('profile.destroy.value'), [
                '_token' => csrf_token(),
                'value' => $value
            ]);
    
            // Verify redirected back properly
            $response->assertRedirect('/profile/edit/values');
        }

        // And check that the defaults is showing up on the profile page
        $response = $this->actingAs($user)->get(route('profile'));
        $response->assertStatus(200);
        $array = [
            'Click to add!', 'Such as:', 'Honesty', 'Friendship', 'Compassion', 'Etc...',
        ];
        foreach($array as $value)
        {
            $response->assertSee($value);
        }
    }

    /**
     * Tests changing rules
     *
     * @return void
     * @test
     */
    public function testRules()
    {
        // Create test user and set setting id
        $user = User::factory()->create();
        $setting_id = Setting::HABITS_START_OF_WEEK;

        // Set show habits hsitory for to current week, as that's the only time start of week matters
        $user_setting = new UsersSettings();
        $user_setting->user_id = $user->id;
        $user_setting->setting_id = Setting::PROFILE_SHOW_RULES;
        $user_setting->value = true;
        $this->assertTrue($user_setting->save());

        // Call profile and verify it can be seen
        $response = $this->actingAs($user)->get(route('profile'));
        $response->assertStatus(200);
        foreach($user->rules as $rule)
        {
            $response->assertSee($rule);
        }

        // Check the edit form shows it
        $response = $this->actingAs($user)->get(route('profile.edit.rules'));
        $response->assertStatus(200);
        foreach($user->rules as $rule)
        {
            $response->assertSee($rule, true);
        }

        // Add a value
        $response = $this->actingAs($user)->post(route('profile.update.rules'), [
            '_token' => csrf_token(),
            'rule' => $this->faker->word(),
        ]);

        // Verify redirected back properly
        $response->assertRedirect('/profile/edit/rules');

        // Refresh model
        $user->refresh();

        // And double check its showing up on the profile/edit pages
        $profile_response = $this->actingAs($user)->get(route('profile'));
        $profile_response->assertStatus(200);
        $edit_response = $this->actingAs($user)->get(route('profile.edit.rules'));
        $edit_response->assertStatus(200);
        foreach($user->rules as $rule)
        {
            $profile_response->assertSee($rule);
            $edit_response->assertSee($rule);
        }

        // Delete all of the rules
        foreach($user->rules as $rule)
        {
            $response = $this->actingAs($user)->post(route('profile.destroy.rule'), [
                '_token' => csrf_token(),
                'rule' => $rule
            ]);
    
            // Verify redirected back properly
            $response->assertRedirect('/profile/edit/rules');
        }

        // And check that the defaults is showing up on the profile page
        $response = $this->actingAs($user)->get(route('profile'));
        $response->assertStatus(200);
        $array = [
            'Rules and boundaries help us to do what we really want',
            'They\'re boundries you\'ve already created to protect yourself',
            'They even work great with pushy friends or family!',
            'Click to add! Such as:',
            'I will get to bed before midnight',
            'I will not rent to friends or family',
            'I will compliment one person a day',
            'Etc...',
        ];
        foreach($array as $rule)
        {
            $response->assertSee($rule, false);
        }
    }

    /**
     * Tests changing nutshell
     *
     * @return void
     * @test
     */
    public function testNutshell()
    {
        // Default nutshell message
        $default = 'Click here to add your nutshell; this is where you list the things that are important to you, that you love doing, and that make you who you are!';
        
        // Edit form placeholder
        $placeholder = "This is your nutshell; It's a place to list the things you that are important to you, the things that make you YOU!";

        // Create test user
        $user = User::factory()->create();

        // Call profile and verify it can be seen
        $response = $this->actingAs($user)->get(route('profile'));
        $response->assertStatus(200);
        if(isset($user->nutshell))
        {
            foreach(explode(PHP_EOL, $user->nutshell) as $line)
            {
                $response->assertSee($line);
            }
        }
        else
        {
            $response->assertSee($default);
        }

        // Check the edit form shows it
        $response = $this->actingAs($user)->get(route('profile.edit.nutshell'));
        $response->assertStatus(200);
        if(isset($user->nutshell))
        {
            foreach(explode(PHP_EOL, $user->nutshell) as $line)
            {
                $response->assertSee($line);
            }
        }
        else
        {
            $response->assertSee($placeholder);
        }

        // Check the edit form actually works to update it
        $response = $this->actingAs($user)->post(route('profile.update.nutshell'), [
            '_token' => csrf_token(),
            'nutshell' => $this->faker->paragraph(),
        ]);

        // Verify redirected back properly
        $response->assertRedirect('/profile');

        // Refresh model
        $user->refresh();

        // And double check its showing up on the profile/edit pages
        $profile_response = $this->actingAs($user)->get(route('profile'));
        $profile_response->assertStatus(200);
        $edit_response = $this->actingAs($user)->get(route('profile.edit.nutshell'));
        $edit_response->assertStatus(200);
        foreach(explode(PHP_EOL, $user->nutshell) as $line)
        {
            $profile_response->assertSee($line);
            $edit_response->assertSee($line);
        }
    }
}