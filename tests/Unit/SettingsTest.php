<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

// Constants
use App\Helpers\Constants\Habits\Type as HabitType;
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\Affirmations\Affirmations;
use App\Models\Habits\Habits;
use App\Models\User\User;
use App\Models\User\UsersSettings;
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
    }

    /**
     * Tests rolling 7 days vs current week
     *
     * @return void
     * @test
     */
    public function testShowHabitHistoryFor()
    {
        // Create test user and set setting id
        $user = User::factory()->create();
        $setting_id = Setting::HABITS_DAYS_TO_DISPLAY;

        // Double check default setting
        $default = config('settings.default');
        $this->assertEquals($default[$setting_id], $user->getSettingValue($setting_id));

        // Call settings and verify it can be seen
        $response = $this->actingAs($user)->get(route('profile.edit.settings'));
        $response->assertStatus(200);
        $response->assertSee(config('settings.options')[$setting_id][Setting::HABITS_ROLLING_SEVEN_DAYS]);

        // Check the edit form actually works to update it
        $response = $this->actingAs($user)->post(route('profile.update.settings', ['id' => $setting_id]), [
            '_token' => csrf_token(),
            'value' => Setting::HABITS_CURRENT_WEEK,
        ]);

        // Verify redirected back properly
        $response->assertRedirect("/profile/edit/settings?#$setting_id");

        // Also, might as well update the timezone before we refresh the model
        $user->timezone = 'America/Denver';
        $user->save();

        // Refresh model
        $user->refresh();

        // And double check the user is returning the new value
        $this->assertEquals(Setting::HABITS_CURRENT_WEEK, $user->getSettingValue($setting_id));

        // Create habit and history
        $habit = Habits::factory()->create();
        while($habit->history()->count() === 0)
        {
            $habit->generateFakeHistory();
        }

        // Get the history array
        $history_array = $habit->getHistoryArray();

        // Test that it's returing rolling 7 days
        $now = new Carbon('now', 'America/Denver');
        foreach(CarbonPeriod::create((clone $now)->subDays(6)->format('Y-m-d'), (clone $now)->format('Y-m-d')) as $carbon)
        {
            $this->assertEquals($carbon->format('D'), $history_array[$carbon->format('w')]['label']);
        }

        // Change setting back, and test again
        $response = $this->actingAs($user)->post(route('profile.update.settings', ['id' => $setting_id]), [
            '_token' => csrf_token(),
            'value' => Setting::HABITS_ROLLING_SEVEN_DAYS,
        ]);

        // Verify redirected back properly
        $response->assertRedirect("/profile/edit/settings?#$setting_id");

        // Refresh models
        $user->refresh();
        $habit->refresh();

        // And double check the user is returning the new value
        $this->assertEquals(Setting::HABITS_ROLLING_SEVEN_DAYS, $user->getSettingValue($setting_id));

        // Check that it's returning current week in the array used to build the view
        $history_array = $habit->getHistoryArray();
        foreach(CarbonPeriod::create((clone $now)->startOfWeek()->format('Y-m-d'), (clone $now)->endOfWeek()->format('Y-m-d')) as $carbon)
        {
            $this->assertEquals($carbon->format('D'), $history_array[$carbon->format('w')]['label']);
        }
    }

    /**
     * Tests different days of the week as the start
     *
     * @return void
     * @test
     */
    public function testHabitsStartOfWeek()
    {
        // Create test user and set setting id
        $user = User::factory()->create();
        $setting_id = Setting::HABITS_START_OF_WEEK;

        // Set show habits hsitory for to current week, as that's the only time start of week matters
        $user_setting = new UsersSettings();
        $user_setting->user_id = $user->id;
        $user_setting->setting_id = Setting::HABITS_DAYS_TO_DISPLAY;
        $user_setting->value = Setting::HABITS_CURRENT_WEEK;
        $this->assertTrue($user_setting->save());

        // Double check default setting
        $default = config('settings.default');
        $this->assertEquals($default[$setting_id], $user->getSettingValue($setting_id));

        // Call settings and verify it can be seen
        $response = $this->actingAs($user)->get(route('profile.edit.settings'));
        $response->assertStatus(200);
        $response->assertSee(config('settings.options')[$setting_id][Setting::HABITS_SUNDAY]);

        // Check the edit form actually works to update it
        $response = $this->actingAs($user)->post(route('profile.update.settings', ['id' => $setting_id]), [
            '_token' => csrf_token(),
            'value' => Setting::HABITS_MONDAY,
        ]);

        // Verify redirected back properly
        $response->assertRedirect("/profile/edit/settings?#$setting_id");

        // Also, might as well update the timezone before we refresh the model
        $user->timezone = 'America/Denver';
        $user->save();

        // Refresh model
        $user->refresh();

        // And double check the user is returning the new value
        $this->assertEquals(Setting::HABITS_MONDAY, $user->getSettingValue($setting_id));

        // Create habit and history
        $habit = Habits::factory()->create();
        while($habit->history()->count() === 0)
        {
            $habit->generateFakeHistory();
        }

        // Get the history array
        $history_array = $habit->getHistoryArray();

        // Test that Monday is first
        foreach($history_array as $key => $value)
        {
            $this->assertEquals($key, Setting::HABITS_MONDAY);
            break;
        }

        // Change setting back, and test again
        $response = $this->actingAs($user)->post(route('profile.update.settings', ['id' => $setting_id]), [
            '_token' => csrf_token(),
            'value' => Setting::HABITS_SUNDAY,
        ]);

        // Verify redirected back properly
        $response->assertRedirect("/profile/edit/settings?#$setting_id");

        // Refresh models
        $user->refresh();
        $habit->refresh();

        // And double check the user is returning the new value
        $this->assertEquals(Setting::HABITS_SUNDAY, $user->getSettingValue($setting_id));

        // Test that sunday is first
        $history_array = $habit->getHistoryArray();
        foreach($history_array as $key => $value)
        {
            $this->assertEquals($key, Setting::HABITS_SUNDAY);
            break;
        }
    }
}