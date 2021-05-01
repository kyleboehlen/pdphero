<?php

namespace App\Models\Goal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Goal\Type;
use App\Helpers\Constants\Goal\TimePeriod;

// Models
use App\Models\Goal\GoalActionItem;
use App\Models\Goal\GoalCategory;
use App\Models\Goal\GoalStatus;

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
            case Type::MANUAL_GOAL:
                $progress = ($this->manual_completed / $this->custom_times) * 100;
                break;
        }

        $this->progress = round($progress);

        return $this->save();
    }

    public function determineStatus()
    {
        // If the start date is before today, then it's TBD
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
            $end_date = $carbon->format('Y-m-d');
            $array['end_date'] = $carbon->format('n/j/y');

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
        $this->save();
    }

    // RELATIONSHIPS
    public function actionItems()
    {
        return $this->hasMany(GoalActionItem::class, 'goal_id', 'id')->whereNotNull('deadline')->orderBy('deadline');
    }

    public function adHocItems()
    {
        return $this->hasMany(GoalActionItem::class, 'goal_id', 'id')->whereNull('deadline')->orderBy('deadline');
    }

    public function category()
    {
        return $this->hasOne(GoalCategory::class, 'id', 'category_id');
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
        return $this->hasMany(Goal::class, 'parent_id', 'id');
    }
}
