<?php

use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Habits\HistoryType as HabitsHistoryType;
use App\Helpers\Constants\Habits\Type as HabitsType;
use App\Helpers\Constants\ToDo\Type as ToDoType;


// Models
use App\Models\Habits\Habits;
use App\Models\Relationships\HabitsToDo;
use App\Models\Todo\ToDo;

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

if(!function_exists('buildHabitToDos'))
{
    /**
     * Builds ToDo items for habits that push to the todo list
     *
     * @return bool
     */
    function buildHabitToDos($user)
    {
        // Track how we do
        $failures = 0;

        // Create a carbon obj for the user
        $timezone = $user->timezone ?? 'America/Denver'; // Should probably change this default someday
        $now = new Carbon('now', $timezone);

        // Get all of the user's (user generate) habits with it's todo relationship loaded
        $habits = Habits::where('user_id', $user->id)->where('type_id', HabitsType::USER_GENERATED)->with('todos')->get();

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
                    // Create the todo items if they don't exsist
                    if($habit->todos->count() < 2)
                    {
                        $completed_todo = new ToDo([
                            'user_id' => $user->id,
                            'title' => $habit->name,
                            'type_id' => ToDoType::HABIT_ITEM,
                            'notes' => "Automatically generated To-Do item for $habit->name",
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
                            'type_id' => ToDoType::HABIT_ITEM,
                            'notes' => "Automatically generated To-Do item for $habit->name",
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
                    foreach($habit->todos as $todo)
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
                foreach($habit->todos as $todo)
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