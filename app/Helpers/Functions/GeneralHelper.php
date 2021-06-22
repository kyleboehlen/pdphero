<?php

use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Goal\Type as GoalType;
use App\Helpers\Constants\Habits\HistoryType as HabitsHistoryType;
use App\Helpers\Constants\Habits\Type as HabitsType;
use App\Helpers\Constants\ToDo\Type as ToDoType;


// Models
use App\Models\Goal\Goal;
use App\Models\Habits\Habits;
use App\Models\Relationships\GoalActionItemsToDo;
use App\Models\Relationships\HabitsToDo;
use App\Models\ToDo\ToDo;

if(!function_exists('buildSocialUrl'))
{
    /**
     * Builds a social url based on the social's config array
     *
     * @return bool
     */

    function buildSocialUrl($social_config_array)
    {
        $url = $social_config_array['url'];
        
        // Check each value in the array to see if it belongs in the url
        foreach($social_config_array as $key => $value)
        {
            $url = str_replace('{' . $key . '}', $value, $url);
        }

        return $url;
    }
}

if(!function_exists('urlIsLogin'))
{
    /**
     * Checks if the URL is the login URL of the application
     *
     * @return bool
     */
    function urlIsLogin($url)
    {
        return $url === url(route('login'));
    }
}

if(!function_exists('urlIsRoot'))
{
    /**
     * Checks if the URL is the root URL of the application
     *
     * @return bool
     */
    function urlIsRoot($url)
    {
        return $url === config('app.url');
    }
}

if(!function_exists('configArrayFromSeededCollection'))
{
    /**
     * Unsets the ID from an array of arrays
     *
     * @return bool
     */
    function configArrayFromSeededCollection($collection)
    {
        return
        array_map(
            function($array){
                unset($array['id']);
                return $array;
            },
            $collection->keyBy('id')->forget('id')->toArray()
        );
    }
}

if(!function_exists('dayOfWeek'))
{
    /**
     * Gets the day of week based on the php datetime value 'w'
     *
     * @return bool
     */
    function dayOfWeek($day)
    {
        return date('l', strtotime("Sunday +{$day} days"));
    }
}

if(!function_exists('buildRecurringHabitToDos'))
{
    /**
     * Builds ToDo items for habits that push to the todo list
     *
     * @return bool
     */
    function buildRecurringHabitToDos($user)
    {
        // Track how we do
        $failures = 0;

        // Create a carbon obj for the user
        $timezone = $user->timezone ?? 'America/Denver'; // Should probably change this default someday
        $now = new Carbon('now', $timezone);

        // Get all of the user's (user generate) habits with it's todo relationship loaded
        $habits = Habits::where('user_id', $user->id)->with('recurringTodos')->get();

        // Iterate through the habits
        foreach($habits as $habit)
        {
            // If push is turned on...
            $delete_todos = false;
            if($habit->show_todo)
            {
                // Check if it's required
                $history_array = $habit->getHistoryArray();
                $history_entry = $history_array[$now->format('w')];
                if($history_entry['required'])
                {
                    // Determine what type of To-Do it will be
                    if($habit->type_id == HabitsType::USER_GENERATED)
                    {
                        $insert_type_id = ToDoType::RECURRING_HABIT_ITEM;
                    }
                    elseif($habit->type_id == HabitsType::AFFIRMATIONS_HABIT)
                    {
                        $insert_type_id = ToDoType::AFFIRMATIONS_HABIT_ITEM;
                    }
                    elseif($habit->type_id == HabitsType::JOURNALING_HABIT)
                    {
                        $insert_type_id = ToDoType::JOURNAL_HABIT_ITEM;
                    }

                    // Create the todo items if they don't exsist
                    if($habit->recurringTodos->count() < 2)
                    {
                        $completed_todo = new ToDo([
                            'user_id' => $user->id,
                            'title' => $habit->name,
                            'type_id' => $insert_type_id,
                            'notes' => "Automatically generated To-Do item for $habit->name" . PHP_EOL . PHP_EOL . $habit->notes,
                            'completed' => true,
                        ]);

                        if(!$completed_todo->save())
                        {
                            $failures++;
                        }
                        else
                        {
                            if(!HabitsToDo::create([
                                'habits_id' => $habit->id,
                                'to_do_id' => $completed_todo->id,
                            ]))
                            {
                                $failures++;
                            }
                        }

                        $pending_todo = new ToDo([
                            'user_id' => $user->id,
                            'title' => $habit->name,
                            'type_id' => $insert_type_id,
                            'notes' => "Automatically generated To-Do item for $habit->name" . PHP_EOL . PHP_EOL . $habit->notes,
                            'completed' => false,
                        ]);

                        if(!$pending_todo->save())
                        {
                            $failures++;
                        }
                        else
                        {
                            if(!HabitsToDo::create([
                                'habits_id' => $habit->id,
                                'to_do_id' => $pending_todo->id,
                            ]))
                            {
                                $failures++;
                            }
                        }

                        $habit->refresh();
                    }

                    // Iterate through the todo items and update accordingly
                    foreach($habit->recurringTodos as $todo)
                    {
                        switch($history_entry['status'])
                        {
                            case HabitsHistoryType::COMPLETED:
                                    if($todo->completed)
                                    {
                                        // Restore the completed one and set the proper name
                                        if($todo->trashed())
                                        {
                                            if(!$todo->restore())
                                            {
                                                $failures++;
                                            }
                                        }

                                        $todo->title = $habit->name;
                                        if(!$todo->save())
                                        {
                                            $failures++;
                                        }
                                    }
                                    else // Non-completed to-do item
                                    {
                                        if(!$todo->delete())
                                        {
                                            $failures++;
                                        }
                                    }
                                break;

                            case HabitsHistoryType::PARTIAL:
                                // Restore if deleted
                                if($todo->trashed())
                                {
                                    if(!$todo->restore())
                                    {
                                        $failures++;
                                    }
                                }

                                // Generate label
                                $times = $history_entry['times'];
                                if($todo->completed)
                                {
                                    $label = "$habit->name ($times out of $habit->times_daily)";
                                }
                                else // Not completed todo
                                {
                                    $times_left = $habit->times_daily - $times;
                                    if($times_left > 1)
                                    {
                                        $label = "$habit->name ($times_left more times)";
                                    }
                                    else
                                    {
                                        $label = "$habit->name ($times_left more time)";
                                    }
                                }

                                // Update label
                                $todo->title = $label;
                                if(!$todo->save())
                                {
                                    $failures++;
                                }
                                break;
                            
                            case HabitsHistoryType::TBD:
                                // Restore if not the completed one
                                if(!$todo->completed)
                                    {
                                        // Restore the completed one and set the proper name
                                        if($todo->trashed())
                                        {
                                            if(!$todo->restore())
                                            {
                                                $failures++;
                                            }
                                        }

                                        $todo->title = $habit->name;
                                        if($habit->times_daily > 1)
                                        {
                                            $todo->title .= " ($habit->times_daily more times)";
                                        }

                                        if(!$todo->save())
                                        {
                                            $failures++;
                                        }
                                    }
                                    else // Completed to-do item
                                    {
                                        if(!$todo->delete())
                                        {
                                            $failures++;
                                        }
                                    }
                                break;

                            default: // Including missed and skipped
                                $delete_todos = true;
                                break;
                        }
                    }
                }
                else // Not required today 
                {
                    $delete_todos = true;
                }
            }
            else // If push is turned off, delete any todos it has
            {
                $delete_todos = true;
            }

            if($delete_todos)
            {
                foreach($habit->recurringTodos as $todo)
                {
                    if(!$todo->trashed())
                    {
                        if(!$todo->delete())
                        {
                            $failures++;
                        }
                    }
                }
            }
        }

        // Return success/failure
        if($failures > 0)
        {
            return $failures;
        }

        // Success!
        return true;
    }
}

if(!function_exists('buildActionItemTodos'))
{
    /**
     * Builds ToDo items for action items that push to the todo list
     * 
     * @return bool
     */
    function buildActionItemTodos($user)
    {
        // Track how we do
        $failures = 0;

        // Create a user date
        $timezone = $user->timezone ?? 'America/Denver'; // Should probably change this default someday
        $user_date = new Carbon('now', $timezone);

        // Get action item goals that belong to user w/ action items
        $goals = Goal::where('user_id', $user->id)->whereIn('type_id', [GoalType::ACTION_AD_HOC, GoalType::ACTION_DETAILED])->with('actionItems')->get();

        // Iterate through the goals
        foreach($goals as $goal)
        {
            if($goal->actionItems->count() > 0)
            {
                // Get current action item todos
                $action_item_array = GoalActionItemsTodo::whereIn('action_item_id', $goal->actionItems->pluck('id'))->get()->pluck('action_item_id')->toArray();

                // Iterate through the action items
                foreach($goal->actionItems as $action_item)
                {
                    // Skip if it's already been created or if it's already been completed
                    if(!in_array($action_item->id, $action_item_array) && !$action_item->achieved)
                    {
                        // Instantiate vars
                        $push_todo = false;
                        $days_to_deadline = 0;

                        // Get the push todo settings for the action item
                        if(!is_null($action_item->override_show_todo)) // if there are push todo settings on the action item
                        {
                            $push_todo = true;
                            $days_to_deadline = $action_item->override_todo_days_before;
                        }
                        else // Check the default push to do settings for the goal
                        {
                            $default_for_goal = $goal->defaultPushTodo();
                            if($default_for_goal !== false)
                            {
                                $push_todo = true;
                                $days_to_deadline = $default_for_goal;
                            }
                        }

                        // Determine if we should create the todo
                        $create_todo = false; // Start by assuming we're not creating the todo
                        if($push_todo)
                        {
                            // Create deadline carbon
                            $deadline = Carbon::parse($action_item->deadline)->setTimezone($timezone);

                            // If we're already past the deadline, push it
                            if($user_date->greaterThan($deadline))
                            {
                                $create_todo = true;
                            }
                            else // Check if we're within the push to do days setting
                            {
                                $diff_in_days = $deadline->diffInDays($user_date);

                                if($diff_in_days <= $days_to_deadline)
                                {
                                    $create_todo = true;
                                }
                            }
                        }

                        if($create_todo)
                        {
                            // Create the todo and relationship
                            $action_item_todo = new ToDo([
                                'user_id' => $user->id,
                                'title' => $action_item->name,
                                'type_id' => ToDoType::ACTION_ITEM,
                                'notes' => "Automatically generated for goal ($goal->name) action item $action_item->name" . PHP_EOL . PHP_EOL . $action_item->notes,
                                'completed' => false,
                            ]);
    
                            if(!$action_item_todo->save())
                            {
                                $failures++;
                            }
                            else
                            {
                                if(!GoalActionItemsToDo::create([
                                    'action_item_id' => $action_item->id,
                                    'to_do_id' => $action_item_todo->id,
                                ]))
                                {
                                    $failures++;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Return success/failure
        if($failures > 0)
        {
            return $failures;
        }

        // Success!
        return true;
    }
}

if(!function_exists('isLowPercentage'))
{
    /**
     * Determines whether or not the strength is considered a low percentage based on the low percentage cut of value
     * 
     * @return bool
     */
    function isLowPercentage($percent)
    {
        // 0% is a special case
        if($percent == 0)
        {
            return false;
        }

        return $percent < config('general.progress_bar.low_percentage_cut_off');
    }
}

if(!function_exists('getPadding'))
{
    /**
     * Calculates the padding for the percent label
     * 
     * @return integer
     */
    function getPadding($percent)
    {
        return isLowPercentage($percent) ? $percent : 0;
    }
}

if(!function_exists('getRGB'))
{
    /**
     * Gets the RGB values for the progress background color based on percent
     * 
     * @return string
     */
    function getRGB($percent)
    {
        // 0% is a special case, no background color
        if($percent == 0)
        {
            return '';
        }

        $red = $percent < 50 ? 255 : floor(255 - ($percent * 2 - 100) * 255 / 100);
        $green = $percent > 50 ? 255 : floor(($percent * 2) * 255 / 100);

        return "rgb($red, $green, 0)";
    }
}
