<?php

namespace App\Models\ToDo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Bus\DispatchesJobs;
use JamesMills\Uuid\HasUuidTrait;
use Carbon\Carbon;
use Log;

// Constants
use App\Helpers\Constants\Habits\HistoryType;
use App\Helpers\Constants\ToDo\Type;

// Jobs
use App\Jobs\CalculateHabitStrength;

// Models
use App\Models\Goal\GoalActionItem;
use App\Models\Habits\HabitHistory;
use App\Models\Habits\Habits;
use App\Models\ToDo\ToDoPriority;
use App\Models\Relationships\GoalActionItemsToDo;

class ToDo extends Model
{
    use DispatchesJobs, HasFactory, HasUuidTrait, SoftDeletes;

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
            case Type::RECURRING_HABIT_ITEM:
            case Type::SINGULAR_HABIT_ITEM:
                // Get habit history entry for the day
                $user = \Auth::user();
                $timezone = $user->timezone ?? 'America/Denver'; // Again... weird default
                $now = new Carbon('now', $timezone);
                $day = $now->startOfDay()->setTimezone('UTC')->format('Y-m-d');
                $habit = $this->habits->first();
                $queued_habit_strength = new CalculateHabitStrength($habit);
                $history_entry = HabitHistory::where('habit_id', $habit->id)->where('day', $day)->first();

                // Increment/Decrement habit history it references (Refreshing of the actual todo items happens in index)
                if($this->completed)
                {
                    // Subtract times
                    $history_entry->times--;

                    // If we're back to 0, just delete the entry
                    if($history_entry->times <= 0)
                    {
                        $success = $history_entry->delete();

                        // Dispatch update strength job
                        $this->dispatch($queued_habit_strength);

                        if($this->type_id == Type::SINGULAR_HABIT_ITEM)
                        {
                            $this->completed = !$this->completed;
        
                            // Save this, and modify saved if it fails
                            return ($success && $this->save());
                        }

                        return $success;
                    }
                }
                else // Not completed
                {
                    // If null, create a new one
                    if(is_null($history_entry))
                    {
                        $history_entry = new HabitHistory([
                            'habit_id' => $habit->id,
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

                    // Increment times as long as we're not surpassing the habit's times daily
                    if($history_entry->times < $habit->times_daily)
                    {
                        $history_entry->times++;
                    }
                }

                // save the history habit
                $saved = $history_entry->save();

                // If it saved properly, and it's a singular habit, toggle completed
                if($saved && $this->type_id == Type::SINGULAR_HABIT_ITEM)
                {
                    $this->completed = !$this->completed;

                    // Save this, and modify saved if it fails
                    $saved = ($saved && $this->save());
                }

                // Dispatch update strength job
                $this->dispatch($queued_habit_strength);

                // Return success based on if everything saved or not
                return $saved;
                break;

            case Type::ACTION_ITEM:
                // Toggle completed status
                $this->completed = !$this->completed;

                // Set action item to match
                $this->load('actionItem');
                $this->actionItem->achieved = $this->completed;
                $this->actionItem->save();

                // Update progress
                $this->actionItem->load('goal');
                $this->actionItem->goal->calculateProgress();
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

    public function actionItem()
    {
        return $this->hasOneThrough(GoalActionItem::class, GoalActionItemsToDo::class, 'to_do_id', 'id', 'id', 'action_item_id');
    }

    // Create the habit relationship
    public function habits()
    {
        return $this->belongsToMany(Habits::class);
    }
}
