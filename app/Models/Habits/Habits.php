<?php

namespace App\Models\Habits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

// Constants
use App\Helpers\Constants\Habits\HistoryType;
use App\Helpers\Constants\Habits\Type;
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\Affirmations\AffirmationsReadLog;
use App\Models\Habits\HabitHistory;
use App\Models\Habits\HabitHistoryTypes;

class Habits extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'days_of_week' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'type_id', 'name', 'times_daily', 'show_todo',
    ];

    /**
     * Set the threshold for the low percentage styling
     * 
     * @var integer
     */
    private $low_percentage_cut_off = 15;

    /**
     * For calculating the strength of a habit
     * 
     * @return bool
     */
    public function calculateStrength()
    {
        // Check if it has history
        if($this->history->count() > 0)
        {
            // For now, we're just gonna set the strength to a random percentage
            $this->strength = rand(1, 100);

            // To-Do: The proper strength calculating algorithm

            // Return failure/success
            return $this->save();
        }

        // Return success, no need to calculate strength
        return true;
    }

    /**
     * For building the history toggle form stuff
     * 
     * @return array
     */
    public function getHistoryArray($offset = 0, $label_format = 'D')
    {
        // Get current user
        $user = \Auth::user();

        // Get the current datetime based on user's timezone if available
        $timezone = $user->timezone ?? 'America/Denver'; // We should really probably use a different default... we'll wait to find out how well the timezones work
        $now = new Carbon('now', $timezone);

        // Set the start and ends dates of the range to pull habit history for based on user's setting
        switch($user->getSettingValue(Setting::HABITS_DAYS_TO_DISPLAY))
        {
            case Setting::HABITS_ROLLING_SEVEN_DAYS:
                $start_date = (clone $now)->subDays(6);
                $end_date = (clone $now);
                break;

            case Setting::HABITS_CURRENT_WEEK:
                $start_date = (clone $now)->startOfWeek();
                $end_date = (clone $now)->endOfWeek();
                break;
        }

        // Set offset
        if($offset > 0)
        {
            // Change week offset into how many days back to go
            $offset *= 7;

            // Offset dates
            $start_date->subDays($offset);
            $end_date->subDays($offset);
        }

        // Populate the history to search, affirmations habits are a special case
        if($this->type_id == Type::AFFIRMATIONS_HABIT)
        {
            $history = $this->buildAffirmationsHistory();
        }
        else
        {
            $history = $this->history;
        }

        // Create a carbon period to iterate through and build history array to return
        $history_array = array();
        $carbon_period = CarbonPeriod::create($start_date->format('Y-m-d'), $end_date->format('Y-m-d'));
        foreach($carbon_period as $carbon)
        {
            // Instantiate the variables we're determining
            $required = null;
            $status = null;

            // Get carbon date in user's timezone
            $user_date = new Carbon($carbon->format('Y-m-d'), $timezone);
            $user_date->hour = $now->hour;
            $user_date->minute = $now->minute;

            // Get day in UTC to search for history
            $search_day = (clone $user_date)->setTimezone('UTC');

            // Determine required based on how we calculate this habit
            if(!is_null($this->days_of_week))
            {
                // Calculate by days of week
                $required = in_array($user_date->format('w'), $this->days_of_week);
            }
            elseif(!is_null($this->every_x_days))
            {
                // Check if this habit has been completed before, and when
                $last_completed = null;
                $key = $history->search(function($h_e) use ($search_day){
                    return $h_e->type_id == HistoryType::COMPLETED && Carbon::parse($h_e->day)->lessThanOrEqualTo($search_day);
                });
                if($key !== false)
                {
                    $last_completed = $history[$key];
                }

                // If it's never been completed...
                if(is_null($last_completed))
                {
                    $required = true;
                }
                else
                {
                    // if the last time it was completed was today
                    $last_completed_carbon = new Carbon($last_completed->day, 'UTC');
                    if(
                        (clone $last_completed_carbon)->setTimezone($timezone)->isSameDay($now) &&
                        // and the search day is also today, we need to go back again to determine
                        (clone $search_day)->setTimezone($timezone)->isSameDay($now)
                    )
                    {
                        // Check for the last time it was completed before that
                        $last_completed = null;
                        $key = $history->search(function($h_e) use ($last_completed_carbon){
                            return $h_e->type_id == HistoryType::COMPLETED && Carbon::parse($h_e->day)->lessThan($last_completed_carbon);
                        });
                        if($key !== false)
                        {
                            $last_completed = $history[$key];
                            $last_completed_carbon = new Carbon($last_completed->day, 'UTC');
                            $days_since_last_completed = $last_completed_carbon->diffInDays($search_day) + 1;
                        }
                        else
                        {
                            // Set days since last completed to the habits times daily in order to make it required
                            $days_since_last_completed = $this->every_x_days;
                        }
                    }
                    else
                    {
                        // Determine days since it was last completed based on search date and last completed
                        $last_completed_carbon = new Carbon($last_completed->day, 'UTC');
                        $days_since_last_completed = $last_completed_carbon->diffInDays($search_day) + 1;
                    }

                    // Determine required based on how many days since it's last been completed
                    if($days_since_last_completed < $this->every_x_days || $days_since_last_completed % $this->every_x_days != 0) // if it hasn't been x days yet or if it's not a multiple of today
                    {
                        // Then it's not required
                        $required = false;
                    }
                    else // Otherwise it's required on this day
                    {
                        $required = true;
                    }
                }
            }

            // Get the history entry for the day we're checking
            $history_entry = null;
            $key = $history->search(function($h) use ($search_day){
                return $h->day == $search_day->format('Y-m-d');
            });
            if($key !== false)
            {
                $history_entry = $history[$key];
            }

            // Figure out status
            if(!is_null($history_entry)) // If we do have history for this habit and day...
            {
                $status = $history_entry->type_id; // Then we also already have the status

                // If it was a completed day, but they didn't do it enough times that day
                if($status == HistoryType::COMPLETED && $history_entry->times < $this->times_daily)
                {
                    // Then it's partial
                    $status = HistoryType::PARTIAL;
                }
            }
            else // If there is no history
            {
                // if this day is today or in the future
                if($user_date->greaterThanOrEqualTo((clone $now)->startOfDay()))
                {
                    // The history status is to be determined!
                    $status = HistoryType::TBD;
                }
                else // if the day is already past
                {
                    // status is base soley on whether or not it was a required day
                    $status = $required ? HistoryType::MISSED : HistoryType::SKIPPED;
                }
            }

            // Add to the array that we're returning
            $history_array[$user_date->format('w')] = [
                'classes' => config('habits.statuses')[$status]['style_class'] . ($required ? '' : ' not-required'),
                'label' => $user_date->format($label_format),
                'required' => $required,
                'status' => $status,
            ];
        }

        return $history_array;
    }

    /**
     * For building the collection of history data for the affirmations habit that matches the habit histories
     * 
     * @return array
     */
    private function buildAffirmationsHistory()
    {
        // Get all the user's affirmations logs
        $affirmation_logs = AffirmationsReadLog::where('user_id', $this->user_id)->orderBy('read_at', 'desc')->get();

        $history_log_array = array();
        foreach($affirmation_logs as $affirmation_log)
        {
            if(array_key_exists($affirmation_log->read_at_key, $history_log_array))
            {
                $history_log_array[$affirmation_log->read_at_key]['times'] += 1; // Increment times that day
            }
            else // create an index in the entry and populate it
            {
                $history_log_array[$affirmation_log->read_at_key] = [
                    'type_id' => HistoryType::COMPLETED,
                    'day' => $affirmation_log->read_at_key,
                    'times' => 1,
                ];
            }
        }

        // Cast to habit history
        $history = collect();
        foreach($history_log_array as $history_log)
        {
            $history->push(new HabitHistory($history_log));
        }

        return $history;
    }

    /**
     * Calculates current streak
     * 
     * @return integer
     */
    public function getCurrentStreak()
    {
        // Get history
        if($this->type_id == Type::AFFIRMATIONS_HABIT)
        {
            $history = $this->buildAffirmationsHistory();
        }
        else
        {
            $history = $this->history;
        }

        $current_streak = 0;
        foreach($history as $history_entry)
        {
            if($history_entry->type_id == HistoryType::COMPLETED)
            {
                $current_streak++;
            }
            elseif($history_entry->type_id == HistoryType::SKIPPED)
            {
                continue;
            }
            else
            {
                break;
            }
        }

        return $current_streak;
    }

    /**
     * Calculates longest streak
     * 
     * @return integer
     */
    public function getLongestStreak()
    {
        // Get history
        if($this->type_id == Type::AFFIRMATIONS_HABIT)
        {
            $history = $this->buildAffirmationsHistory();
        }
        else
        {
            $history = $this->history;
        }

        $longest_streak = 0;
        $current_streak = 0;
        foreach($history as $history_entry)
        {
            if($history_entry->type_id == HistoryType::COMPLETED)
            {
                $current_streak++;
            }
            elseif($history_entry->type_id == HistoryType::SKIPPED)
            {
                continue;
            }
            else
            {
                if($current_streak > $longest_streak)
                {
                    $longest_streak = $current_streak;
                }
                
                $current_streak = 0;
            }
        }

        return $longest_streak;
    }

    /**
     * Calculates the padding for the percent label
     * 
     * @return integer
     */
    public function getPadding()
    {
        return $this->isLowPercentage() ? $this->strength : 0;
    }

    /**
     * Gets the RGB values for the progress background color based on percent
     * 
     * @return string
     */
    public function getRGB()
    {
        // 0% is a special case, no background color
        if($this->strength == 0)
        {
            return '';
        }

        $percent = $this->strength;

        $red = $percent < 50 ? 255 : floor(255 - ($percent * 2 - 100) * 255 / 100);
        $green = $percent > 50 ? 255 : floor(($percent * 2) * 255 / 100);

        return "rgb($red, $green, 0)";
    }

    // Habit history relationship
    public function history()
    {
        return $this->hasMany(HabitHistory::class, 'habit_id', 'id')->orderBy('day', 'desc');
    }

    /**
     * Determines whether or not the habit is considered a low percentage based on the low percentage cut of value
     * 
     * @return bool
     */
    public function isLowPercentage()
    {
        // 0% is a special case
        if($this->strength == 0)
        {
            return false;
        }

        return $this->strength < $this->low_percentage_cut_off;
    }

    /**
     * For generating test history (or the lack thereof)
     * 
     * @return bool
     */
    public function generateFakeHistory()
    {
        // For some habits, we're just not going to create a history and the strength will stay at 0
        if(array_rand([true, false])) // Random chance of creating history or not
        {
            return true; // Tell the test we succeeded (at creating nothing, go us!)
        }

        // Testing with America/Denver
        $carbon = new Carbon('now', 'America/Denver');

        // Go back a random amount of days for a start date and create a carbon period
        $days_back = rand(1, 365);
        $carbon_period = CarbonPeriod::create(
            (clone $carbon)->subDays($days_back)->setTimezone('UTC')->format('Y-m-d'),
            (clone $carbon)->setTimezone('UTC')->format('Y-m-d'),
        );
        

        foreach($carbon_period as $day)
        {
            // Determine a random history type
            $type = HabitHistoryTypes::inRandomOrder()->first();

            // Create the property array for creating the habit history
            $habit_history_properties = [
                'habit_id' => $this->id,
                'day' => $day->format('Y-m-d'),
                'type_id' => $type->id,
            ];

            // Set other history properties based on history type
            switch($type->id)
            {
                case HistoryType::COMPLETED:
                    $habit_history_properties['times'] = rand(1, $this->times_daily);
                    break;
                
                case HistoryType::SKIPPED:
                    // Use DB default on times
                    // Notes are required
                    $habit_history_properties['notes'] = 'Mandatory notes';
                    break;

                case HistoryType::MISSED:
                    // Sometimes on missed we just won't generate data at all
                    if(array_rand([true, false]))
                    {
                        continue 2;
                    }

                    // Set missed amount of times
                    $habit_history_properties['times'] = 0;
                    break;
            }

            // Randomly create notes on some of the other statuses as well
            if(rand(1, 50) == 10) // Cuz I can, that's why.
            {
                $habit_history_properties['notes'] = 'Random notes';
            }

            // Create history data
            $history = new HabitHistory($habit_history_properties);

            // Save history data
            if(!$history->save())
            {
                // Return failure
                return false;
            }
        }

        // Return success
        return true;
    }
}
