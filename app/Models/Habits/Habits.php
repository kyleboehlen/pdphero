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

// Models
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
        return $this->hasMany(HabitHistory::class, 'habit_id', 'id');
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
