<?php

namespace App\Models\Goal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;
use Carbon\Carbon;
use Log;

// Constants
use App\Helpers\Constants\Goal\Status;
use App\Helpers\Constants\Goal\Type;
use App\Helpers\Constants\Goal\TimePeriod;

// Models
use App\Models\Bucketlist\BucketlistItem;
use App\Models\Goal\GoalActionItem;
use App\Models\Goal\GoalCategory;
use App\Models\Goal\GoalStatus;
use App\Models\Habits\Habits;

class Goal extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    protected $fillable = [
        'name', 'reason', 'type_id', 'user_id', 'status_id',
    ];

    /**
     * For calculating the strength of a goal
     * 
     * @return bool
     */
    public function calculateProgress()
    {
        $progress = 0;

        switch($this->type_id)
        {
            case Type::PARENT_GOAL:
                $this->load('subGoals');
                if($this->subGoals->count() > 0)
                {
                    $progress = ($this->subGoals->sum('progressMax100') / ($this->subGoals->count() * 100)) * 100;
                }
                else
                {
                    $progress = 0;
                }
                break;

            case Type::ACTION_AD_HOC:
            case Type::BUCKETLIST:
                // Set vars
                $achieved_count = 0;
                $total_count = 0;

                // Iterate through ad hoc array to set vars
                foreach($this->getAdHocArray() as $array)
                {
                    $achieved_count += $array['action_items']->sum('achieved');
                    $total_count += $this->custom_times;
                }

                // Calculate progress
                if($total_count == 0)
                {
                    $progress = 0;
                }
                else
                {
                    $progress = ($achieved_count / $total_count) * 100;
                }
                break;
            
            case Type::ACTION_DETAILED:
                // Get completed action items
                $achieved_count = $this->actionItems()->where('achieved', 1)->get()->count();

                // Get total action items
                $total_count = $this->actionItems()->get()->count();

                // Calculate progress
                if($total_count == 0)
                {
                    $progress = 0;
                }
                else
                {
                    $progress = ($achieved_count / $total_count) * 100;
                }
                break;
            
            case Type::HABIT_BASED:
                $this->load('habit');
                $progress = ($this->habit->strength / $this->habit_strength) * 100;
                break;

            case Type::MANUAL_GOAL:
                // Calculate progress, allow > 100% progress on manual goals
                $progress = ($this->manual_completed / $this->custom_times) * 100;
                break;
        }

        $this->progress = round($progress);

        // Recursively update parent goals if needed
        if(!is_null($this->parent_id))
        {
            $success = $this->save() && $this->parent->calculateProgress();
        }
        else
        {
            $success = $this->save();
        }

        // Update status
        if(!$this->determineStatus())
        {
            Log::error('Failed to determine status for goal after updating goal progress', $this->toArray());
        }

        return $success;
    }

    public function determineStatus()
    {
        // Get logged in user's date
        $user = \Auth::user();
        $timezone = $user->timezone ?? 'America/Denver';
        $user_date = Carbon::now($timezone);

        // We don't set status on future goals
        if($this->type_id == Type::FUTURE_GOAL)
        {
            return true; // Return success
        }


        // If start date is before now, it's TBD
        if(!is_null($this->start_date) && Carbon::parse($this->start_date)->setTimezone($timezone)->greaterThan($user_date))
        {
            $this->status_id = Status::TBD;
        }
        elseif($this->progress >= 100) // if it's above 100% progress, it's completed. Not achieved, but completed
        {
            $this->status_id = Status::COMPLETED;
        }
        else
        {
            // Start by setting status to on track as a default
            $this->status_id = Status::ON_TRACK;

            // And then determine ahead/lagging based on goal
            switch($this->type_id)
            {
                case Type::HABIT_BASED:
                    // Run strength evaluations on goal habit
                    $this->load('habit');
                    $evaluation_array = $this->habit->evaluateStrengthCalculations(false, $this->habit_strength);
                    $days_to_strength = $evaluation_array['actual_days'];

                    // Get goal end date and see if we've already passed it
                    $end_date = Carbon::parse($this->end_date)->setTimezone($timezone);
                    if($end_date->lessThan($user_date))
                    {
                        $diff_in_days = $end_date->diffInDays($user_date);
                        if($diff_in_days + $days_to_strength > config('goals.lagging_buffer.days'))
                        {
                            $this->status_id = Status::LAGGING;
                        }
                    }
                    else // We haven't gotten to the end date yet
                    {
                        // How many days do we have to reach the habit strength?
                        $days_to_end_date = $user_date->diffInDays($end_date);

                        // Subtract how many days it would take to reach the strength goal
                        $diff_in_days = $days_to_end_date - $days_to_strength;

                        if($diff_in_days < 0) // We might be lagging
                        {
                            $diff_in_days = abs($diff_in_days);

                            // Check if we're lagging hard enough to trigger the lagging status
                            if($diff_in_days > config('goals.lagging_buffer.days'))
                            {
                                $this->status_id = Status::LAGGING;
                            }
                        }
                        else // We might be ahead
                        {
                            // Check if we're far enough ahead to trigger the ahead status
                            if($diff_in_days > config('goals.ahead_buffer.days'))
                            {
                                $this->status_id = Status::AHEAD;
                            }
                        }
                    }
                    break;

                // Attempt to set status based on due date of action, fall back to percentage otherwise
                case Type::ACTION_AD_HOC:
                case Type::ACTION_DETAILED:
                case Type::BUCKETLIST:
                    // Get the action item with the earliest deadline
                    $action_item = $this->actionItems()->where('achieved', 0)->first();

                    // Check if deadline is already past
                    if(!is_null($action_item))
                    {
                        $deadline = Carbon::parse($action_item->deadline)->setTimezone($timezone);
                        if($deadline->lessThan($user_date)) // Lagging
                        {
                            // Check if it's above the threshold that triggers lagging
                            $diff_in_days = $deadline->diffInDays($user_date);
                            if($diff_in_days > config('goals.lagging_buffer.days'))
                            {
                                $this->status_id = Status::LAGGING;
                            }
                        }
                        else // Possibly ahead of schedule
                        {
                            // Get the last completed action item
                            $action_item = $this->actionItems('desc')->where('achieved', 1)->first();

                            // See if it has been completed before the deadline
                            if(!is_null($action_item))
                            {
                                $deadline = Carbon::parse($action_item->deadline)->setTimezone($timezone);
                                if($deadline->greaterThan($user_date))
                                {
                                    // Check if it's above the threshold that triggers ahead
                                    $diff_in_days = $deadline->diffInDays($user_date);
                                    if($diff_in_days > config('goals.ahead_buffer.days'))
                                    {
                                        $this->status_id = Status::AHEAD;
                                    }
                                }
                            }
                        }
                        break;
                    }

                case Type::PARENT_GOAL:
                case Type::ACTION_AD_HOC:
                case Type::MANUAL_GOAL:
                case Type::BUCKETLIST:
                    // Get carbon objects for goal start/end dates
                    $start_date = Carbon::parse($this->start_date)->setTimezone($timezone);
                    $end_date = Carbon::parse($this->end_date)->setTimezone($timezone);

                    // Figure out the total amount of days between start/end date
                    $goal_length_in_days = $start_date->diffInDays($end_date);

                    // And number of days elapsed since start date
                    if($user_date->lessThan($end_date))
                    {
                        $goal_progress_in_days = $start_date->diffInDays($user_date);
                    }
                    else
                    {
                        $goal_progress_in_days = $goal_length_in_days;
                    }

                    // Determine what percentage of total days have elapsed, this is the progress they should be at to be on track
                    $on_track_progress = ($goal_progress_in_days / $goal_length_in_days) * 100;

                    // Determine the difference in the actual progress and the on track progress
                    $progress_diff = $this->progress - $on_track_progress;

                    // If it's less than 0
                    if($progress_diff < 0) // It's lagging
                    {
                        $progress_diff = abs($progress_diff);

                        // Check if it's above the threshold that triggers the lagging status
                        if($progress_diff >= config('goals.lagging_buffer.percent'))
                        {
                            $this->status_id = Status::LAGGING;
                        }
                    }
                    else // It might be ahead of schedule
                    {
                        // Check if it's above the threshold that triggers the ahead status
                        if($progress_diff >= config('goals.ahead_buffer.percent'))
                        {
                            $this->status_id = Status::AHEAD;
                        }
                    }
                    break;
            }
        }

        
        return $this->save();
    }

    public function defaultPushTodo()
    {
        if(!is_null($this->default_show_todo))
        {
            return $this->default_todo_days_before;
        }
        elseif(!is_null($this->parent_id))
        {
            $this->load('parent');
            if(!is_null($this->parent))
            {
                return $this->parent->defaultPushTodo();
            }
        }

        return false;
    }

    public function getAdHocArray()
    {
        $ad_hoc_array = array();
        $carbon = Carbon::parse($this->start_date);
        $carbon_end = Carbon::parse($this->end_date);

        while($carbon->lessThan($carbon_end))
        {
            // Set start date
            $start_date = $carbon->format('Y-m-d');
            $array = [
                'start_date' => $carbon->format('n/j/y'),
            ];

            // Set end date
            switch($this->time_period_id)
            {
                case TimePeriod::WEEKLY:
                    $carbon->addWeek();
                    break;
                
                case TimePeriod::BI_WEEKLY:
                    $carbon->addWeeks(2);
                    break;

                case TimePeriod::MONTHLY:
                    $carbon->addMonth();
                    break;

                case TimePeriod::QUARTERLY:
                    $carbon->addQuarter();
                    break;

                case TimePeriod::YEARLY:
                    $carbon->addYear();
                    break;
                
                case TimePeriod::TOTAL:
                    $carbon = Carbon::parse($this->end_date);
                default:
                    break;
            }

            $carbon->subDay();
            $end_date = $carbon->format('Y-m-d');
            $array['end_date'] = $carbon->format('n/j/y');
            $carbon->addDay();

            // Get action items
            $array['action_items'] = $this->actionItems()->whereBetween('deadline', [$start_date, $end_date])->get();

            // Push
            array_push($ad_hoc_array, $array);
        }

        return $ad_hoc_array;
    }

    public function getScope()
    {
        if($this->type_id == Type::FUTURE_GOAL)
        {
            return 'future';
        }
        elseif($this->achieved)
        {
            return 'achieved';
        }
        
        return 'active';
    }

    public function shiftDates($days, $action = 'add')
    {
        // Shift dates on this goal
        if(!is_null($this->start_date))
        {
            $start_carbon = Carbon::parse($this->start_date);

            if($action == 'add')
            {
                $start_carbon->addDays($days);
            }
            else
            {
                $start_carbon->subDays($days);
            }

            $this->start_date = $start_carbon->format('Y-m-d');
        }

        if(!is_null($this->end_date))
        {
            $end_carbon = Carbon::parse($this->end_date);

            if($action == 'add')
            {
                $end_carbon->addDays($days);
            }
            else
            {
                $end_carbon->subDays($days);
            }

            $this->end_date = $end_carbon->format('Y-m-d');
        }

        // If parent goal, shift dates on sub-goals
        if($this->type_id == Type::PARENT_GOAL)
        {
            $this->load('subGoals');
            foreach($this->subGoals as $sub_goal)
            {
                $sub_goal->shiftDates($days, $action);
            }
        }

        // If ad-hoc goals, shift dates on action items
        if($this->type_id == Type::ACTION_DETAILED)
        {
            $this->load('actionItems');
            foreach($this->actionItems as $action_item)
            {
                $action_item->shiftDeadline($days, $action);
            }
        }

        // Save
        $success = $this->save();

        if(!$this->determineStatus())
        {
            Log::error('Failed to determine status for goal after shifting dates', $this->toArray());
        }

        return $success;
    }

    public function getProgressMax100Attribute()
    {
        if($this->progress > 100)
        {
            return 100;
        }

        return $this->progress;
    }

    public function loadBucketlistActionItems()
    {
        $this->actionItems = $this->actionItems()->get();
    }

    // RELATIONSHIPS
    public function actionItems($order = 'asc')
    {
        if($this->type_id == Type::BUCKETLIST)
        {
            return $this->hasMany(BucketlistItem::class, 'goal_id', 'id')->whereNotNull('deadline')->orderBy('deadline', $order);
        }

        return $this->hasMany(GoalActionItem::class, 'goal_id', 'id')->whereNotNull('deadline')->orderBy('deadline', $order);
    }

    public function loadBucketlistAdHocItems()
    {
        $this->adHocItems =
            BucketlistItem::whereNull('goal_id')->whereNull('deadline')
                ->where('user_id', $this->user_id)->where('achieved', 0)
                ->orderBy('name')->get();
    }

    public function adHocItems()
    {
        return $this->hasMany(GoalActionItem::class, 'goal_id', 'id')->whereNull('deadline')->orderBy('name');
    }

    public function category()
    {
        return $this->hasOne(GoalCategory::class, 'id', 'category_id');
    }

    public function habit()
    {
        return $this->hasOne(Habits::class, 'id', 'habit_id');
    }

    public function parent()
    {
        return $this->hasOne(Goal::class, 'id', 'parent_id');
    }

    public function status()
    {
        return $this->hasOne(GoalStatus::class, 'id', 'status_id');
    }

    public function subGoals()
    {
        return $this->hasMany(Goal::class, 'parent_id', 'id')->orderBy('end_date', 'asc');
    }
}
