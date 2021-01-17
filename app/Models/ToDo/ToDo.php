<?php

namespace App\Models\Todo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;
use Carbon\Carbon;
use Log;

// Models
use App\Models\ToDo\ToDoPriority;

class ToDo extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    public function priority()
    {
        return $this->hasOne(ToDoPriority::class, 'id', 'priority_id');
    }

    /**
     * Toggles the completed attribute and
     * returns the success of saving the model
     *
     * @return bool
     */
    public function toggleCompleted()
    {
        // Toggle completed status
        $this->completed = !$this->completed;

        // return success/failure
        return $this->save();
    }

    /**
     * Returns yesterday/today/day
     *
     * @return string
     */
    public function relativeUpdatedAt()
    {
        $updated_at = Carbon::parse($this->updated_at);

        if($updated_at->isToday())
        {
            return 'today';
        }
        elseif($updated_at->isYesterday())
        {
            return 'yesterday';
        }
        elseif(!$updated_at->isLastWeek())
        {
            return 'on ' . $updated_at->englishDayOfWeek();
        }

        return 'on ' . $update_at->format('m/d/y');
    }
}
