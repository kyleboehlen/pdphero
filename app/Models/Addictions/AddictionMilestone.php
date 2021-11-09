<?php

namespace App\Models\Addictions;

use Illuminate\Database\Eloquent\Model;
use JamesMills\Uuid\HasUuidTrait;

// Constants
use App\Helpers\Constants\Addiction\DateFormat;

// Models
use App\Models\Addictions\Addiction;

class AddictionMilestone extends Model
{
    use HasUuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'addiction_id', 'name', 'amount', 'date_format_id', 'reward'
    ];

    public function dateFromCarbon($carbon)
    {
        switch($this->date_format_id)
        {
            case DateFormat::MINUTE:
                $carbon->addMinutes($this->amount);
                break;
            
            case DateFormat::HOUR:
                $carbon->addHours($this->amount);
                break;

            case DateFormat::DAY:
                $carbon->addDays($this->amount);
                break;

            case DateFormat::MONTH:
                $carbon->addMonths($this->amount);
                break;

            case DateFormat::YEAR:
                $carbon->addYears($this->amount);
                break;
        }

        return $carbon;
    }

    // Relationships
    public function addiction()
    {
        return $this->hasOne(Addiction::class, 'id', 'addiction_id');
    }
}
