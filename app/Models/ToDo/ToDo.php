<?php

namespace App\Models\ToDo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;
use Carbon\Carbon;
use Log;

// Constants
use App\Helpers\Constants\Habits\HistoryType;
use App\Helpers\Constants\ToDo\Type;

// Models
use App\Models\Habits\HabitHistory;
use App\Models\Habits\Habits;
use App\Models\ToDo\ToDoPriority;

class ToDo extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'title', 'type_id', 'notes', 'completed',
    ];

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
        // Check what type of to do it is
        switch($this->type_id)
        {
            case Type::HABIT_ITEM:
                // Get habit history entry for the day
                $user = \Auth::user();
                $timezone = $user->timezone ?? 'America/Denver'; // Again... weird default
                $now = new Carbon('now', $timezone);
                $day = $now->startOfDay()->setTimezone('UTC')->format('Y-m-d');
                $habit_id = $this->habits->first()->id;
                $history_entry = HabitHistory::where('habit_id', $habit_id)->where('day', $day)->first();

                // Increment/Decrement habit history it references (Refreshing of the actual todo items happens in index)
                if($this->completed)
                {
                    // Subtract times
                    $history_entry->times--;

                    // If we're back to 0, just delete the entry
                    if($history_entry->times <= 0)
                    {
                        return $history_entry->delete();
                    }
                }
                else // Not completed
                {
                    // If null, create a new one
                    if(is_null($history_entry))
                    {
                        $history_entry = new HabitHistory([
                            'habit_id' => $habit_id,
                            'type_id' => HistoryType::COMPLETED,
                            'day' => $day,
                            'times' => 0,
                        ]);

                        if(!$history_entry->save())
                        {
                            // Return failure
                            return false;
                        }
                    }

                    // Increment times
                    $history_entry->times++;
                }

                // Return whether the habit history saves
                return $history_entry->save();
                break;

            default: // including Type::TODO_ITEM
                // Toggle completed status
                $this->completed = !$this->completed;
                break;
        }

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

    // Create the habit relationship
    public function habits()
    {
        return $this->belongsToMany(Habits::class);
    }
}
