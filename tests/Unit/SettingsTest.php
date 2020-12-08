<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\User\User;
use App\Models\Todo\ToDo;

class SettingsTest extends TestCase
{
    use WithFaker;

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
            'value' => false,
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
            'hours' => $test_hours,
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
}