<?php

namespace Tests\Test;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Constants
use App\Helpers\Constants\Goal\Type as GoalType;

// Models
use App\Models\Affirmations\Affirmations;
use App\Models\Bucketlist\BucketlistItem;
use App\Models\Feature\Feature;
use App\Models\Habits\Habits;
use App\Models\Goal\Goal;
use App\Models\Goal\GoalCategory;
use App\Models\Journal\JournalCategory;
use App\Models\Journal\JournalEntry;
use App\Models\ToDo\ToDo;
use App\Models\User\User;

class TestUserTest extends TestCase
{
    /**
     * Create a test user
     *
     * @test
     */
    public function userTest()
    {
        $user = User::factory()->create([
            'email' => 'test@test.com',
        ]);

        $this->assertNotNull(User::find($user->id));

        return $user;
    }

    /**
     * Generate features
     * 
     * @test
     */
    public function featuresTest()
    {
        Feature::factory(rand(4, 10))->create();

        $this->assertTrue(Feature::all()->count() >= 4);
    }

    /**
     * Give test user Bucketlist items
     * 
     * @depends userTest
     * @test
     */
    public function bucketlistTest(User $user)
    {
        BucketlistItem::factory(rand(10, 30))->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(BucketlistItem::where('user_id', $user->id)->get()->count() >= 5);
    }

    /**
     * Give test user ToDo items
     * 
     * @depends userTest
     * @test
     */
    public function toDoTest(User $user)
    {
        ToDo::factory(rand(10, 30))->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(ToDo::where('user_id', $user->id)->get()->count() >= 5);
    }

    /**
     * Give test user Affirmations
     * 
     * @depends userTest
     * @test
     */
    public function affirmationsTest(User $user)
    {
        Affirmations::factory(rand(3, 7))->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(Affirmations::where('user_id', $user->id)->get()->count() >= 3);
    }

    /**
     * Give test user Habits
     * 
     * @depends userTest
     * @test
     */
    public function habitsTest(User $user)
    {
        Habits::factory(rand(3, 7))->create([
            'user_id' => $user->id,
        ]);

        $habits = Habits::where('user_id', $user->id)->get();

        $this->assertTrue($habits->count() >= 3);

        // Populate some values for each habit
        $test = $this;
        $habits->each(function($habit) use ($test){
            // Generate fake history
            $test->assertTrue($habit->generateFakeHistory());

            // Calculate strength based on fake
            $test->assertTrue($habit->calculateStrength());
        });
    }

    /**
     * Give test user Goals
     * 
     * @depends userTest
     * @test
     */
    public function goalsTest(User $user)
    {
        GoalCategory::factory(rand(1, 4))->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(GoalCategory::where('user_id', $user->id)->get()->count() >= 1);

        foreach(config('goals.types') as $goal_type_id => $whatever)
        {
            $category_id = GoalCategory::where('user_id', $user->id)->inRandomOrder()->first()->id;
            $attr = [
                'user_id' => $user->id,
                'category_id' => $category_id,
            ];
            switch($goal_type_id)
            {
                case GoalType::PARENT_GOAL:
                    Goal::factory(rand(1, 3))->parent()->create($attr);
                    break;

                case GoalType::ACTION_AD_HOC:
                    Goal::factory(rand(1, 3))->adHoc()->create($attr);
                    break;
                
                case GoalType::ACTION_DETAILED:
                    Goal::factory(rand(1, 3))->actionPlan()->create($attr);
                    break;

                case GoalType::HABIT_BASED:
                    $habit_id = Habits::where('user_id', $user->id)->inRandomOrder()->first()->id;
                    $attr['habit_id'] = $habit_id;
                    Goal::factory(rand(1, 3))->habit()->create($attr);
                    break;
                
                case GoalType::FUTURE_GOAL:
                    Goal::factory(rand(1, 3))->future()->create($attr);
                    break;

                case GoalType::MANUAL_GOAL:
                    Goal::factory(rand(1, 3))->manual()->create($attr);
                    break;
            }
        }

        $this->assertTrue(Goal::where('user_id', $user->id)->get()->count() >= count(config('goals.types')));

        foreach(Goal::where('user_id', $user->id)->get() as $goal)
        {
            $this->assertTrue($goal->calculateProgress());
        }
    }

    /**
     * Give test user journal entries and categories
     * 
     * @depends userTest
     * @test
     */
    public function journalTest(User $user)
    {
        // Create default categories
        foreach(config('journal.default_categories') as $category_name)
        {
            $default_category = new JournalCategory([
                'user_id' => $user->id,
                'name' => $category_name,
            ]);

            $this->assertTrue($default_category->save());
        }

        // Create a custom category
        JournalCategory::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(JournalCategory::where('user_id', $user->id)->get()->count() >= count(config('journal.default_categories')) + 1);

        // Generate journal entries
        JournalEntry::factory(rand(200, 300))->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(JournalEntry::where('user_id', $user->id)->get()->count() >= 200);
    }
}
