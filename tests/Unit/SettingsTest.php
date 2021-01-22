<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Habits\Type as HabitType;
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\Affirmations\Affirmations;
use App\Models\Habits\Habits;
use App\Models\User\User;
use App\Models\ToDo\ToDo;

class SettingsTest extends TestCase
{
    use WithFaker;

    /**
     * Tests changing the setting Affirmations show read setting
     *
     * @return void
     * @test
     */
    public function testAffirmationsShowRead()
    {
        // Create test user and set setting id
        $user = User::factory()->create();
        $setting_id = Setting::AFFIRMATIONS_SHOW_READ;

        // Double check default setting
        $default = config('settings.default');
        $this->assertEquals($default[$setting_id], $user->getSettingValue($setting_id));

        // Generate fake affirmations for user
        Affirmations::factory(rand(5, 15))->create([
            'user_id' => $user->id,
        ]);

        // Refresh model
        $user->refresh();

        // Go though all the affirmations and check that the good job page is shown
        foreach($user->affirmations as $affirmation)
        {
            $response = $this->actingAs($user)->get(route('affirmations.show', ['affirmation' => $affirmation->uuid]));
            $response->assertStatus(200);
            usleep(200000);
        }

        // Refresh model
        $user->refresh();

        // Verify that calling the read function returns the read page redirect
        $response = $this->actingAs($user)->post(route('affirmations.read.check'), [
            '_token' => csrf_token(),
        ]);
        $response->assertRedirect('/affirmations/read');

        // Call settings and verify it can be seen
        $response = $this->actingAs($user)->get(route('profile.edit.settings'));
        $response->assertStatus(200);

        // Check the edit form actually works to update it
        $response = $this->actingAs($user)->post(route('profile.update.settings', ['id' => $setting_id]), [
            '_token' => csrf_token(),
        ]);

        // Verify redirected back properly
        $response->assertRedirect("/profile/edit/settings?#$setting_id");

        // Refresh model
        $user->refresh();

        // And double check the user is returning the new value
        $this->assertFalse((bool) $user->getSettingValue($setting_id));

        // Go though all the affirmations and check that the good job page is not shown now
        foreach($user->affirmations as $affirmation)
        {
            $response = $this->actingAs($user)->get(route('affirmations.show', ['affirmation' => $affirmation->uuid]));
            $response->assertStatus(200);
            usleep(200000);
        }

        // Refresh model
        $user->refresh();

        // Verify that calling the read function returns the read page redirect
        $response = $this->actingAs($user)->post(route('affirmations.read.check'), [
            '_token' => csrf_token(),
        ]);
        $response->assertRedirect('/profile');
    }

    /**
     * Tests changing the setting To-Do move completed
     *
     * @return void
     * @test
     */
    public function testTodoMoveCompleted()
    {
        // Create test user and set setting id
        $user = User::factory()->create();
        $setting_id = Setting::TODO_MOVE_COMPLETED;

        // Double check default setting
        $default = config('settings.default');
        $this->assertEquals($default[$setting_id], $user->getSettingValue($setting_id));

        // Call settings and verify it can be seen
        $response = $this->actingAs($user)->get(route('profile.edit.settings'));
        $response->assertStatus(200);

        // Check the edit form actually works to update it
        $response = $this->actingAs($user)->post(route('profile.update.settings', ['id' => $setting_id]), [
            '_token' => csrf_token(),
        ]);

        // Verify redirected back properly
        $response->assertRedirect("/profile/edit/settings?#$setting_id");

        // Refresh model
        $user->refresh();

        // And double check the user is returning the new value
        $this->assertFalse((bool) $user->getSettingValue($setting_id));
    }

    /**
     * Tests changing the setting To-Do move completed
     *
     * @return void
     * @test
     */
    public function testShowCompletedFor()
    {
        $test_hours = 12;

        // Create test user and set setting id
        $user = User::factory()->create();
        $setting_id = Setting::TODO_SHOW_COMPLETED_FOR;

        // Double check default setting
        $default = config('settings.default');
        $this->assertEquals($default[$setting_id], $user->getSettingValue($setting_id));

        // Call settings and verify it can be seen
        $response = $this->actingAs($user)->get(route('profile.edit.settings'));
        $response->assertStatus(200);

        // Check the edit form actually works to update it
        $response = $this->actingAs($user)->post(route('profile.update.settings', ['id' => $setting_id]), [
            '_token' => csrf_token(),
            'value' => $test_hours,
        ]);

        // Verify redirected back properly
        $response->assertRedirect("/profile/edit/settings?#$setting_id");

        // Refresh model
        $user->refresh();

        // And double check the user is returning the new value
        $this->assertEquals($test_hours, $user->getSettingValue($setting_id));

        // Test it with To-do items on the list
        $valid_todo_items = ToDo::factory(rand(15, 60))->create([
            'user_id' => $user->id,
        ]);

        // Create an invalid completed at date
        $datetime_string = Carbon::now()->subHours(++$test_hours)->toDatetimeString();
        $invalid_todo_items = ToDo::factory(rand(15, 60))->create([
            'user_id' => $user->id,
            'completed' => 1,
            'updated_at' => $datetime_string
        ]);

        // Get list and check
        $response = $this->actingAs($user)->get(route('todo.list'));
        $response->assertStatus(200);

        foreach($valid_todo_items as $item)
        {
            $response->assertSee($item->title);
        }

        foreach($invalid_todo_items as $item)
        {
            $response->assertDontSee($item->title);
        }
    }

    /**
     * Tests changing whether or not the affirmations habit is shown
     *
     * @return void
     * @test
     */
    public function testHabitsShowAffirmationsHabit()
    {
        // Create test user and set setting id
        $user = User::factory()->create();
        $setting_id = Setting::HABITS_SHOW_AFFIRMATIONS_HABIT;

        // Double check default setting
        $default = config('settings.default');
        $this->assertEquals($default[$setting_id], $user->getSettingValue($setting_id));

        // Call settings and verify it can be seen
        $response = $this->actingAs($user)->get(route('profile.edit.settings'));
        $response->assertStatus(200);

        // Check the edit form actually works to update it
        $response = $this->actingAs($user)->post(route('profile.update.settings', ['id' => $setting_id]), [
            '_token' => csrf_token(),
            'value' => 'checked',
        ]);

        // Verify redirected back properly
        $response->assertRedirect("/profile/edit/settings?#$setting_id");

        // Refresh model
        $user->refresh();

        // And double check the user is returning the new value
        $this->assertTrue((bool) $user->getSettingValue($setting_id));

        // Call the habits index page
        $response = $this->actingAs($user)->get(route('habits'));
        $response->assertRedirect(route('habits')); // Building affirmation habit for first time

        // Get the newly created affirmations habit and recall the habits route
        $affirmations_habit = Habits::where('user_id', $user->id)->where('type_id', HabitType::AFFIRMATIONS_HABIT)->first();
        $response = $this->actingAs($user)->get(route('habits'));
        $response->assertStatus(200);
        $response->assertSee($affirmations_habit->uuid);
        $response->assertSee($affirmations_habit->name);

        // TO-DO:
        // Check the order of the day labels to test for the setting
        // rolling_seven_days vs current_week
    }
}