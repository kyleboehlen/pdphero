<?php

namespace App\Models\Goal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;

// Constants
use App\Helpers\Constants\Goal\Type;

// Models
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

    // RELATIONSHIPS
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

    public function subgoals()
    {
        return $this->hasMany(Goal::class, 'parent_id', 'id');
    }
}
