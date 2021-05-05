<?php

namespace App\Models\Goal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;
use Carbon\Carbon;

// Models
use App\Models\Goal\Goal;
use App\Models\Relationships\GoalActionItemsToDo;
use App\Models\ToDo\ToDo;

class GoalActionItem extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    protected $fillable = [
        'goal_id', 'name', 'notes',
    ];

    public function shiftDeadline($days, $action = 'add')
    {
        if(!is_null($this->deadline))
        {
            $deadline_carbon = Carbon::parse($this->deadline);

            if($action == 'add')
            {
                $deadline_carbon->addDays($days);
            }
            else
            {
                $deadline_carbon->subDays($days);
            }
    
            $this->deadline = $deadline_carbon->format('Y-m-d');
    
            $this->save();
        }
    }

    public function goal()
    {
        return $this->hasOne(Goal::class, 'id', 'goal_id');
    }

    public function todo()
    {
        return $this->hasOneThrough(ToDo::class, GoalActionItemsToDo::class, 'action_item_id', 'id', 'id', 'to_do_id');
    }
}
