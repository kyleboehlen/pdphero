<?php

namespace Tests\Deploy;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Artisan;
use Schema;
use Hash;
use DB;

// Models
use App\Models\Addictions\AddictionDateFormat;
use App\Models\Addictions\AddictionMethod;
use App\Models\Addictions\AddictionRelapseType;
use App\Models\FirstVisit\FirstVisitMessages;
use App\Models\Goal\GoalTimePeriod;
use App\Models\Goal\GoalType;
use App\Models\Goal\GoalStatus;
use App\Models\Habits\HabitTypes;
use App\Models\Habits\HabitHistoryTypes;
use App\Models\Home\Home;
use App\Models\Journal\JournalMood;
use App\Models\ToDo\ToDoPriority;
use App\Models\ToDo\ToDoTypes;
use App\Models\User\Settings;

class DeployTest extends TestCase
{
    /**
     * Migrate database and verify tables exsist
     *
     * @test
     */
    public function migrateTest()
    {
        // Migrate database
        Artisan::call('migrate');

        // Check if tables exsist
        $this->assertTrue(Schema::hasTable('jobs'));
        $this->assertTrue(Schema::hasTable('failed_jobs'));
        $this->assertTrue(Schema::hasTable('migrations'));
        $this->assertTrue(Schema::hasTable('password_resets'));
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('settings'));
        $this->assertTrue(Schema::hasTable('users_settings'));
        $this->assertTrue(Schema::hasTable('affirmations'));
        $this->assertTrue(Schema::hasTable('affirmations_read_logs'));
        $this->assertTrue(Schema::hasTable('habit_types'));
        $this->assertTrue(Schema::hasTable('habits'));
        $this->assertTrue(Schema::hasTable('habit_history_types'));
        $this->assertTrue(Schema::hasTable('habit_histories'));
        $this->assertTrue(Schema::hasTable('habits_to_do'));
        $this->assertTrue(Schema::hasTable('goal_statuses'));
        $this->assertTrue(Schema::hasTable('goal_categories'));
        $this->assertTrue(Schema::hasTable('goal_types'));
        $this->assertTrue(Schema::hasTable('goal_time_periods'));
        $this->assertTrue(Schema::hasTable('goals'));
        $this->assertTrue(Schema::hasTable('goal_action_items'));
        $this->assertTrue(Schema::hasTable('goal_action_items_to_do'));
        $this->assertTrue(Schema::hasTable('homes'));
        $this->assertTrue(Schema::hasTable('users_hide_homes'));
        $this->assertTrue(Schema::hasTable('journal_categories'));
        $this->assertTrue(Schema::hasTable('journal_moods'));
        $this->assertTrue(Schema::hasTable('journal_entries'));
        $this->assertTrue(Schema::hasTable('first_visit_messages'));
        $this->assertTrue(Schema::hasTable('first_visit_displayed'));
        $this->assertTrue(Schema::hasTable('cache'));
        $this->assertTrue(Schema::hasTable('activities'));
        $this->assertTrue(Schema::hasTable('faqs'));
        $this->assertTrue(Schema::hasTable('features'));
        $this->assertTrue(Schema::hasTable('feature_votes'));
        $this->assertTrue(Schema::hasTable('to_do_categories'));
        $this->assertTrue(Schema::hasTable('push_subscriptions'));
        $this->assertTrue(Schema::hasTable('habit_reminders'));
        $this->assertTrue(Schema::hasTable('goal_action_item_reminders'));
        $this->assertTrue(Schema::hasTable('to_do_reminders'));
        $this->assertTrue(Schema::hasTable('bucketlist_categories'));
        $this->assertTrue(Schema::hasTable('bucketlist_items'));
        $this->assertTrue(Schema::hasTable('sms_limits'));
        $this->assertTrue(Schema::hasTable('addiction_methods'));
        $this->assertTrue(Schema::hasTable('addiction_date_formats'));
        $this->assertTrue(Schema::hasTable('addictions'));
        $this->assertTrue(Schema::hasTable('addiction_relapse_types'));
        $this->assertTrue(Schema::hasTable('addiction_relapses'));
        $this->assertTrue(Schema::hasTable('addiction_milestones'));
    }

    /**
     * Seed the database and verify categories seeded
     * 
     * @test
     */
    public function seedTest()
    {
        // Seed the database
        Artisan::call('db:seed');

        // Verify ToDo Priorities seeded
        $this->assertEquals(configArrayFromSeededCollection(ToDoPriority::all()), config('todo.priorities'));

        // Verify ToDo Types seeded
        $this->assertEquals(configArrayFromSeededCollection(ToDoTypes::all()), config('todo.types'));

        // Verify User Settings seeded
        $this->assertEquals(configArrayFromSeededCollection(Settings::all()), config('settings.seed'));

        // Verify Habit Types seeded
        $this->assertEquals(configArrayFromSeededCollection(HabitTypes::all()), config('habits.types'));

        // Verify Habit History Types seeded
        $this->assertEquals(configArrayFromSeededCollection(HabitHistoryTypes::all()), config('habits.history_types'));

        // Verify Goal Types seeded
        $this->assertEquals(configArrayFromSeededCollection(GoalType::all()), config('goals.types'));

        // Verify Goal Statuses seeded
        $this->assertEquals(configArrayFromSeededCollection(GoalStatus::all()), config('goals.statuses'));

        // Verify Goal Ad Hoc Periods seeded
        $this->assertEquals(configArrayFromSeededCollection(GoalTimePeriod::all()), config('goals.time_periods'));

        // Verify Home seeded
        $this->assertEquals(configArrayFromSeededCollection(Home::all()), config('home'));

        // Verify Journal moods
        $this->assertEquals(configArrayFromSeededCollection(JournalMood::all()), config('journal.moods'));

        // First visit messages
        $this->assertEquals(configArrayFromSeededCollection(FirstVisitMessages::all()), config('first-visit.messages'));

        // Addiction methods
        $this->assertEquals(configArrayFromSeededCollection(AddictionMethod::all()), config('addictions.methods'));

        // Addiction date formats
        $this->assertEquals(configArrayFromSeededCollection(AddictionDateFormat::all()), config('addictions.date_formats'));


        // Addiction relapse types
        $this->assertEquals(configArrayFromSeededCollection(AddictionRelapseType::all()), config('addictions.relapse.types'));
    }
}
