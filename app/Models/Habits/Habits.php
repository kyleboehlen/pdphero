<?php

namespace App\Models\Habits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use KyleBoehlen\Uuid\HasUuidTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Log\Log;

// Constants
use App\Helpers\Constants\Habits\HistoryType;
use App\Helpers\Constants\Habits\Type;
use App\Helpers\Constants\ToDo\Type as ToDoType;
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\Affirmations\AffirmationsReadLog;
use App\Models\Goal\Goal;
use App\Models\Habits\HabitHistory;
use App\Models\Habits\HabitHistoryTypes;
use App\Models\Habits\HabitReminder;
use App\Models\Relationships\HabitsToDo;
use App\Models\Journal\JournalEntry;
use App\Models\ToDo\ToDo;
use App\Models\User\User;

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
     * For calculating the strength of a habit
     * 
     * @return bool
     */
    public function calculateStrength()
    {
        // Clear cache values
        Cache::put("habit-$this->id-longest-streak", null, -5);
        Cache::put("habit-$this->id-current-streak", null, -5);
        Cache::put("habit-$this->id-history-asc", null, -5);
        Cache::put("habit-$this->id-history-desc", null, -5);

        // Get current user
        $user = $this->user;

        // Get the current datetime based on user's timezone if available
        $timezone = $user->timezone ?? 'America/Denver'; // We should really probably use a different default... we'll wait to find out how well the timezones work
        $now = new Carbon('now', $timezone);

        // Get ascending history
        $history = $this->getHistory(true);
        if($history->count() == 0)
        {
            // If theres no history set the strength to 0
            $strength = 0;

            // Return success/failure based on saving the model
            return $this->save();
        }

        // Build the dates needed to iterate through history
        $first_history_day = $history->first()->day; // Be sure we're going out past the first day of history we have
        $start_date = new Carbon($first_history_day, $timezone);
        $start_date->subDay(); // Grace day to be safe with timezones
        $end_date = (clone $now);

        // Build the carbon period to iterate through
        $carbon_period = CarbonPeriod::create($start_date->format('Y-m-d'), $end_date->format('Y-m-d'));

        // Instantate positive/negative change and set strength to 0
        $negative_change = config('habits.strength.min_day_change');
        $positive_change = config('habits.strength.min_day_change');
        $strength = 0;

        // Iterate through dates and build strength
        foreach($carbon_period as $carbon)
        {
            // Get carbon date in user's timezone
            $user_date = new Carbon($carbon->format('Y-m-d'), $timezone);

            // Get day in UTC to search for history
            $search_day = (clone $user_date)->setTimezone('UTC');

            // Set hour/min for proper user date functionality
            $user_date->hour = $now->hour;
            $user_date->minute = $now->minute;

            // Check if it was required that day
            $required = $this->isRequired($user_date, $search_day, $timezone, $now);

            // If it was, lets calculate how it effects strength
            // if($required)
            // {
                $history_entry = null;
                $key = $history->search(function($h) use ($search_day){
                    return $h->day == $search_day->format('Y-m-d');
                }); // Find the history entry for that day
                if($key !== false) // If it's set
                {
                    // Assign it to history entry for use later
                    $history_entry = $history[$key];
                }

                // If there is no history for that day
                if(is_null($history_entry))
                {
                    if($required)
                    {
                        // Then they missed the day
                        $status = HistoryType::MISSED;
                    }
                    else
                    {
                        // Then they missed the day
                        $status = HistoryType::SKIPPED;
                    }
                }
                else // If we do have history for that day
                {
                    // Assign the status from the history type
                    $status = $history_entry->type_id;
                }

                switch($status)
                {
                    // If it was completed, adjust the positive change up and negative change down
                    case HistoryType::COMPLETED:
                        $positive_change += $positive_change * config('habits.strength.change_rate');
                        $negative_change -= $negative_change * config('habits.strength.change_rate');
                        break;

                    case HistoryType::SKIPPED:
                        // Do nothing, almost like it was skipped!
                        break;

                    // If it was missed, adjust the positive change down and negative change up
                    case HistoryType::MISSED:
                        $positive_change -= $positive_change * config('habits.strength.change_rate');
                        $negative_change += $negative_change * config('habits.strength.change_rate');
                        break;
                }

                // Verify positive change isn't higher than the max day change
                if($positive_change > config('habits.strength.max_day_change'))
                {
                    $positive_change = config('habits.strength.max_day_change');
                }
                elseif($positive_change < config('habits.strength.min_day_change')) // Or lower
                {
                    $postive_change = config('habits.strength.min_day_change');
                }

                // Verify negative change isn't higher than the max day change
                if($negative_change > config('habits.strength.max_day_change'))
                {
                    $negative_change = config('habits.strength.max_day_change');
                }
                elseif($negative_change < config('habits.strength.min_day_change')) // Or lower
                {
                    $negative_change = config('habits.strength.min_day_change');
                }

                // Check for partial and calculate partial percent
                if($status == HistoryType::COMPLETED && $history_entry->times < $this->times_daily)
                {
                    // Then it's partial
                    $status = HistoryType::PARTIAL;
                    $partial_percentage = $history_entry->times / $this->times_daily; // Detemine a partial adjustment for strength
                }

                switch($status)
                {
                    case HistoryType::COMPLETED:
                        $strength += $positive_change; // If completed add positive change
                        break;

                    case HistoryType::PARTIAL:
                        $strength += ($positive_change * $partial_percentage); // If partial, only give partial postive change
                        break;

                    case HistoryType::MISSED:
                        $strength -= $negative_change; // And if missed, negative change
                        break;
                }

                // If the strength is above the max strength allowed by the strength buffer
                $max_strength = 100 + config('habits.strength.buffer');
                if($strength > $max_strength)
                {
                    $strength = $max_strength;
                }
                elseif($strength < 0) // And make sure we don't have negative strength
                {
                    $strength = 0;
                }
            // }
        }

        // If the strength was in the buffer we're only going to save it as 100%
        if($strength > 100)
        {
            $strength = 100;
        }

        // Set strength
        $this->strength = round($strength);

        // And set the success/failure of saving the model
        $success = $this->save();

        // Update the progress of any goals associated with this habit
        $this->load('goals');
        foreach($this->goals as $goal)
        {
            if(!$goal->calculateProgress())
            {
                Log::error('Failed to calculate progress for goal after updating habit strength', $goal->toArray());
            }
        }

        return $success;
    }

    /**
     * For building the history toggle form stuff
     * 
     * @return array
     */
    public function getHistoryArray($offset = 0, $label_format = 'D')
    {
        // Get current user
        $user = $this->user;

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
                if($user->getSettingValue(Setting::HABITS_START_OF_WEEK) == Setting::HABITS_MONDAY)
                {
                    $start_date = (clone $now)->startOfWeek(Carbon::MONDAY);
                    $end_date = (clone $now)->endOfWeek(Carbon::SUNDAY);
                }
                else // Setting::HABITS_SUNDAY
                {
                    $start_date = (clone $now)->startOfWeek(Carbon::SUNDAY);
                    $end_date = (clone $now)->endOfWeek(Carbon::SATURDAY);
                }
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

        $history = $this->getHistory();

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

            // Get day in UTC to search for history
            $search_day = (clone $user_date)->setTimezone('UTC');

            // Set hour/min for proper user date functionality
            $user_date->hour = $now->hour;
            $user_date->minute = $now->minute;

            $required = $this->isRequired($user_date, $search_day, $timezone, $now);

            // Get the history entry for the day we're checking
            $history_entry = null;
            $times = 0;
            $key = $history->search(function($h) use ($search_day){
                return $h->day == $search_day->format('Y-m-d');
            });
            if($key !== false)
            {
                $history_entry = $history[$key];
                $times = $history_entry->times;
            }

            // Figure out status
            if(!is_null($history_entry)) // If we do have history for this habit and day...
            {
                $status = $history_entry->type_id; // Then we also already have the status

                // Can't miss a day that's not required
                if($status == HistoryType::MISSED && !$required)
                {
                    $status = HistoryType::SKIPPED;
                }

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
                'carbon' => $user_date,
                'notes' => is_null($history_entry) ? null : $history_entry->notes,
                'times' => $times,
            ];
        }

        return $history_array;
    }

    /**
     * This returns stats about the strength algoritm
     * 
     * @return array
     */
    public function evaluateStrengthCalculations($verbose = false, $strength_cap = 100)
    {
        $strength = $this->strength;
        $days = 0;
        $days_to_cap = 0;
        $progress_change = config('habits.strength.min_day_change');
        while($strength < $strength_cap)
        {
            $days++;
            $strength += $progress_change;
            $progress_change += $progress_change * config('habits.strength.change_rate');
            if($progress_change < config('habits.strength.max_day_change'))
            {
                $days_to_cap = $days;
            }
            else
            {
                $progress_change = config('habits.strength.max_day_change');
            }
            if($verbose)
            {
                echo "($days): Strength: " . round($strength) . '| Progress Change Rate:' . round($progress_change, 2) . "\n";
            }
        }
        
        if(!is_null($this->days_of_week))
        {
            $actual_days = (int) ceil(($days / count($this->days_of_week)) * 7);
        }
        else
        {
            $actual_days = $days * $this->every_x_days;
        }

        return [
            'strength' => $strength,
            'strength_cap' => $strength_cap,
            'days' => $days,
            'actual_days' => $actual_days,
            'days_to_progress_cap' => $days_to_cap,
        ];
    }

    /**
     * This is a public function to allow checking if a notif is required
     * 
     * @return array
     */
    public function notificationRequired($now)
    {
        $timezone = $now->getTimezone()->getName();
        $search_day = (clone $now)->setTimezone('UTC');

        return $this->isRequired($now, $search_day, $timezone, $now);
    }

    /**
     * Returns history for that type of habit
     * 
     * @return Collection
     */
    private function getHistory($asc = false)
    {
        // Populate the history to search, affirmations habits are a special case
        if($this->type_id == Type::AFFIRMATIONS_HABIT)
        {
            $history = $this->buildAffirmationsHistory($asc);
        }
        elseif($this->type_id == Type::JOURNALING_HABIT)
        {
            $history = $this->buildJournalingHistory($asc);
        }
        else
        {
            if($asc)
            {
                $history = $this->historyAsc;
            }
            else
            {
                $history = $this->history;
            }
        }

        return $history;
    }

    /**
     * Checks whether habit is required on a certain day or not
     * 
     * @return bool
     */
    private function isRequired($user_date, $search_day, $timezone, $now)
    {
        $history = $this->getHistory();

        // Determine required based on how we calculate this habit
        if(!is_null($this->days_of_week))
        {
            // Calculate by days of week
            $required = in_array($user_date->format('w'), $this->days_of_week);
        }
        elseif(!is_null($this->every_x_days))
        {
            // If its required every single day
            if($this->every_x_days == 1)
            {
                // Then it's required
                $required = true;
            }
            else
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
                    // if the last time it was completed was the search day
                    $last_completed_carbon = new Carbon($last_completed->day, 'UTC');
                    if(
                        $last_completed->day == $search_day->format('Y-m-d') 
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
                        $days_since_last_completed = $last_completed_carbon->diffInDays($search_day);
                    }

                    // Determine required based on how many days since it's last been completed
                    if($days_since_last_completed < $this->every_x_days || 
                        ($search_day->greaterThan($now) && $days_since_last_completed % $this->every_x_days != 0)) // if it hasn't been x days yet or if it's not a multiple of today
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
        }

        return $required;
    }

    /**
     * For building the collection of history data for the affirmations habit that matches the habit histories
     * 
     * @return array
     */
    private function buildAffirmationsHistory($asc = false)
    {
        // Check cache
        if($asc)
        {
            $cache_key = "habit-$this->id-history-asc";
        }
        else
        {
            $cache_key = "habit-$this->id-history-desc";
        }

        if(Cache::has($cache_key))
        {
            return Cache::get($cache_key);
        }

        // Get current user
        $user = $this->user;

        // Get the current datetime based on user's timezone if available
        $timezone = $user->timezone ?? 'America/Denver'; // We should really probably use a different default... we'll wait to find out how well the timezones work
        $end_date = new Carbon('now', $timezone);

        // Get all the user's affirmations logs
        $history = collect();
        $affirmation_logs = AffirmationsReadLog::where('user_id', $user->id);
        if($asc)
        {
            $affirmation_logs = $affirmation_logs->orderBy('read_at')->get();

            if($affirmation_logs->count() == 0)
            {
                return $history;
            }

            $start_date = Carbon::parse($affirmation_logs->first()->read_at)->subDay();
            $carbon_period = CarbonPeriod::create($start_date->format('Y-m-d'), $end_date->format('Y-m-d'));
        }
        else
        {
            $affirmation_logs = $affirmation_logs->orderBy('read_at', 'desc')->get();

            if($affirmation_logs->count() == 0)
            {
                return $history;
            }

            $start_date = Carbon::parse($affirmation_logs->last()->read_at)->subDay();
            $carbon_period = 
                array_reverse( // We're reversing it so we can iterate desc instead of ascending
                    CarbonPeriod::create($start_date->format('Y-m-d'), $end_date->format('Y-m-d'))
                    ->toArray()
                );
        }

        $history_log_array = array();
        foreach($carbon_period as $carbon)
        {
            // Get carbon date in user's timezone
            $user_date = new Carbon($carbon->format('Y-m-d'), $timezone);

            // Generate the UTC range for searching for affirmation read logs
            $start_timestamp = (clone $user_date)->startOfDay()->setTimezone('UTC');
            $end_timestamp = (clone $user_date)->endOfDay()->setTimezone('UTC');

            // Filter to that range
            $filtered = $affirmation_logs->filter(function($a_r_l) use ($start_timestamp, $end_timestamp){
                return
                    Carbon::parse($a_r_l->read_at)->lessThanOrEqualTo($end_timestamp) &&
                    Carbon::parse($a_r_l->read_at)->greaterThanOrEqualTo($start_timestamp);
            });

            // If we have something, add it to the array
            $times = $filtered->count();
            if($times > 0)
            {
                array_push($history_log_array, [
                    'type_id' => HistoryType::COMPLETED,
                    'day' => (clone $user_date)->startOfDay()->setTimezone('UTC')->format('Y-m-d'),
                    'times' => $times,
                ]);
            }
        }

        // Cast to habit history
        foreach($history_log_array as $history_log)
        {
            $history->push(new HabitHistory($history_log));
        }

        Cache::put($cache_key, $history);

        return $history;
    }

    /**
     * For building the collection of history data for the journaling habit that matches the habit histories
     * 
     * @return array
     */
    private function buildJournalingHistory($asc = false)
    {
        // Check cache
        if($asc)
        {
            $cache_key = "habit-$this->id-history-asc";
        }
        else
        {
            $cache_key = "habit-$this->id-history-desc";
        }

        if(Cache::has($cache_key))
        {
            return Cache::get($cache_key);
        }

        // Get current user
        $user = $this->user;

        // Get the current datetime based on user's timezone if available
        $timezone = $user->timezone ?? 'America/Denver'; // We should really probably use a different default... we'll wait to find out how well the timezones work
        $end_date = new Carbon('now', $timezone);

        // Get all the user's journal entries
        $history = collect();
        $journal_entries = JournalEntry::where('user_id', $user->id);
        if($asc)
        {
            $journal_entries = $journal_entries->orderBy('created_at')->get();

            if($journal_entries->count() == 0)
            {
                return $history;
            }

            $start_date = Carbon::parse($journal_entries->first()->created_at)->subDay();
            $carbon_period = CarbonPeriod::create($start_date->format('Y-m-d'), $end_date->format('Y-m-d'));
        }
        else
        {
            $journal_entries = $journal_entries->orderBy('created_at', 'desc')->get();

            if($journal_entries->count() == 0)
            {
                return $history;
            }

            $start_date = Carbon::parse($journal_entries->last()->created_at)->subDay();
            $carbon_period = 
                array_reverse( // We're reversing it so we can iterate desc instead of ascending
                    CarbonPeriod::create($start_date->format('Y-m-d'), $end_date->format('Y-m-d'))
                    ->toArray()
                );
        }

        $history_entry_array = array();
        foreach($carbon_period as $carbon)
        {
            // Get carbon date in user's timezone
            $user_date = new Carbon($carbon->format('Y-m-d'), $timezone);

            // Generate the UTC range for searching for affirmation read logs
            $start_timestamp = (clone $user_date)->startOfDay()->setTimezone('UTC');
            $end_timestamp = (clone $user_date)->endOfDay()->setTimezone('UTC');

            // Filter to that range
            $filtered = $journal_entries->filter(function($j_e) use ($start_timestamp, $end_timestamp){
                return
                    Carbon::parse($j_e->created_at)->lessThanOrEqualTo($end_timestamp) &&
                    Carbon::parse($j_e->created_at)->greaterThanOrEqualTo($start_timestamp);
            });

            // If we have something, add it to the array
            $times = $filtered->count();
            if($times > 0)
            {
                array_push($history_entry_array, [
                    'type_id' => HistoryType::COMPLETED,
                    'day' => (clone $user_date)->startOfDay()->setTimezone('UTC')->format('Y-m-d'),
                    'times' => $times,
                ]);
            }
        }

        // Cast to habit history
        foreach($history_entry_array as $history_entry)
        {
            $history->push(new HabitHistory($history_entry));
        }

        Cache::put($cache_key, $history);

        return $history;
    }

    /**
     * Calculates current streak
     * 
     * @return integer
     */
    public function getCurrentStreak()
    {
        // Check cache
        $cached_current_streak = Cache::get("habit-$this->id-current-streak");
        if(!is_null($cached_current_streak))
        {
            return $cached_current_streak;
        }

        // Get current user
        $user = $this->user;

        // Get the current datetime based on user's timezone if available
        $timezone = $user->timezone ?? 'America/Denver'; // We should really probably use a different default... we'll wait to find out how well the timezones work
        $now = new Carbon('now', $timezone);

        // Get descedning history
        $history = $this->getHistory();
        if($history->count() == 0)
        {
            // If theres no history there can't be a streak
            return 0;
        }

        // Build the dates needed to iterate through history
        $first_history_day = $history->last()->day; // Be sure we're going out past the first day of history we have
        $start_date = new Carbon($first_history_day, $timezone);
        $start_date->subDay(); // Grace day to be safe with timezones
        $end_date = (clone $now);

        // Build the carbon period to iterate through
        $carbon_period = 
            array_reverse( // We're reversing it so we can iterate desc instead of ascending
                CarbonPeriod::create($start_date->format('Y-m-d'), $end_date->format('Y-m-d'))
                ->toArray()
            );

        // Iterate through dates and build streak
        $current_streak = 0;
        foreach($carbon_period as $carbon)
        {
            // Get carbon date in user's timezone
            $user_date = new Carbon($carbon->format('Y-m-d'), $timezone);

            // Get day in UTC to search for history
            $search_day = (clone $user_date)->setTimezone('UTC');

            // Set hour/min for proper user date functionality
            $user_date->hour = $now->hour;
            $user_date->minute = $now->minute;

            // Check if it was required that day
            $required = $this->isRequired($user_date, $search_day, $timezone, $now);

            if($required)
            {
                $history_entry = null;
                $key = $history->search(function($h) use ($search_day){
                    return $h->day == $search_day->format('Y-m-d');
                }); // Find the history entry for that day
                if($key !== false) // If it's set
                {
                    // Assign it to history entry for use later
                    $history_entry = $history[$key];
                }

                if(is_null($history_entry))
                {
                    if(!$user_date->isToday())
                    {
                        break;
                    }
                }
                elseif($history_entry->type_id == HistoryType::MISSED)
                {
                    break;
                }
                elseif($history_entry->type_id == HistoryType::COMPLETED)
                {
                    $current_streak++;
                }
            }
        }

        $seconds_left_in_day = $now->diffInSeconds((clone $now)->endOfDay());
        Cache::put("habit-$this->id-current-streak", $current_streak, $seconds_left_in_day);

        return $current_streak;
    }

    /**
     * Calculates longest streak
     * 
     * @return integer
     */
    public function getLongestStreak()
    {
        // Check cache
        $cached_longest_streak = Cache::get("habit-$this->id-longest-streak");
        if(!is_null($cached_longest_streak))
        {
            return $cached_longest_streak;
        }

        // Get current user
        $user = $this->user;

        // Get the current datetime based on user's timezone if available
        $timezone = $user->timezone ?? 'America/Denver'; // We should really probably use a different default... we'll wait to find out how well the timezones work
        $now = new Carbon('now', $timezone);

        // Get ascending history
        $history = $this->getHistory(true);
        if($history->count() == 0)
        {
            // If theres no history there can't be a streak
            return 0;
        }

        // Build the dates needed to iterate through history
        $first_history_day = $history->first()->day; // Be sure we're going out past the first day of history we have
        $start_date = new Carbon($first_history_day, $timezone);
        $start_date->subDay(); // Grace day to be safe with timezones
        $end_date = (clone $now);

        // Build the carbon period to iterate through
        $carbon_period = CarbonPeriod::create($start_date->format('Y-m-d'), $end_date->format('Y-m-d'));

        // Iterate through dates and build streaks
        $longest_streak = 0;
        $current_streak = 0;
        foreach($carbon_period as $carbon)
        {
            // Get carbon date in user's timezone
            $user_date = new Carbon($carbon->format('Y-m-d'), $timezone);

            // Get day in UTC to search for history
            $search_day = (clone $user_date)->setTimezone('UTC');

            // Set hour/min for proper user date functionality
            $user_date->hour = $now->hour;
            $user_date->minute = $now->minute;

            // Check if it was required that day
            $required = $this->isRequired($user_date, $search_day, $timezone, $now);

            if($required)
            {
                $history_entry = null;
                $key = $history->search(function($h) use ($search_day){
                    return $h->day == $search_day->format('Y-m-d');
                }); // Find the history entry for that day
                if($key !== false) // If it's set
                {
                    // Assign it to history entry for use later
                    $history_entry = $history[$key];
                }

                if(is_null($history_entry) || $history_entry->type_id == HistoryType::MISSED)
                {
                    if($current_streak > $longest_streak)
                    {
                        $longest_streak = $current_streak;
                    }
                    
                    $current_streak = 0;
                }
                elseif($history_entry->type_id == HistoryType::COMPLETED)
                {
                    $current_streak++;
                }
            }
        }

        if($current_streak > $longest_streak)
        {
            $longest_streak = $current_streak;
        }

        $seconds_left_in_day = $now->diffInSeconds((clone $now)->endOfDay());
        Cache::put("habit-$this->id-longest-streak", $longest_streak, $seconds_left_in_day);

        return $longest_streak;
    }

    // Goals relationship
    public function goals()
    {
        return $this->hasMany(Goal::class, 'habit_id', 'id');
    }

    // Habit history relationship
    public function history()
    {
        return $this->hasMany(HabitHistory::class, 'habit_id', 'id')->orderBy('day', 'desc');
    }

    // Habit history relationship
    public function historyAsc()
    {
        return $this->hasMany(HabitHistory::class, 'habit_id', 'id')->orderBy('day');
    }

    // Reminders relationship
    public function reminders()
    {
        return $this->hasMany(HabitReminder::class, 'habit_id', 'id')->orderBy('remind_at');
    }

    // ToDo relationship
    public function todos()
    {
        return $this->belongsToMany(ToDo::class)->withTrashed();
    }

    // ToDo relationship
    public function recurringTodos()
    {
        return $this->belongsToMany(ToDo::class)->whereIn('type_id', [
            ToDoType::RECURRING_HABIT_ITEM,
            ToDoType::JOURNAL_HABIT_ITEM,
            ToDoType::AFFIRMATIONS_HABIT_ITEM
        ])->withTrashed();
    }

    // User relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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
            (clone $carbon)->subDays($days_back)->format('Y-m-d'),
            (clone $carbon)->format('Y-m-d'),
        );
        

        foreach($carbon_period as $day)
        {
            // Convert to UTC
            $day->setTimezone('UTC');

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
                    // Randomly do some partials
                    if(rand(1, 3) != 3)
                    {
                        $habit_history_properties['times'] = rand(1, $this->times_daily);
                    }
                    else // Fully completed
                    {
                        $habit_history_properties['times'] = $this->times_daily;
                    }
                    break;
                
                case HistoryType::SKIPPED:
                    // Only skip it 10% of the times this status is choosen
                    if(rand(1, 10) != 5)
                    {
                        // Change it to completed
                        $habit_history_properties['type_id'] = HistoryType::COMPLETED;

                        // Randomly do some partials
                        if(rand(1, 3) != 3)
                        {
                            $habit_history_properties['times'] = rand(1, $this->times_daily);
                        }
                        else // Fully completed
                        {
                            $habit_history_properties['times'] = $this->times_daily;
                        }
                    }

                    // Use DB default on times
                    // Notes are required
                    $habit_history_properties['notes'] = 'Mandatory notes';
                    break;

                case HistoryType::MISSED:
                    // Only miss it 20% of the times this status is choosen
                    if(rand(1, 5) != 5)
                    {
                        // Change it to completed
                        $habit_history_properties['type_id'] = HistoryType::COMPLETED;
                        $habit_history_properties['times'] = $this->times_daily;
                        continue 2;
                    }

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
