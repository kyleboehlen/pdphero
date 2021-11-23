<?php

namespace App\Models\Addictions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Addiction\DateFormat;
use App\Helpers\Constants\Addiction\RelapseType;

// Models
use App\Models\Addictions\AddictionMilestone;
use App\Models\Addictions\AddictionRelpase;
use App\Models\User\User;

class Addiction extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'method_id', 'details', 'start_date',
    ];


    // Relationships
    public function milestones()
    {
        return $this->hasMany(AddictionMilestone::class, 'addiction_id', 'id')->orderBy('date_format_id')->orderBy('amount');
    }

    public function pendingMilestones()
    {
        return $this->hasMany(AddictionMilestone::class, 'addiction_id', 'id')->where('reached', 0)->orderBy('date_format_id')->orderBy('amount');
    }

    public function reachedMilestones()
    {
        return $this->hasMany(AddictionMilestone::class, 'addiction_id', 'id')->where('reached', 1)->orderBy('date_format_id')->orderBy('amount');
    }

    public function relapses()
    {
        return $this->hasMany(AddictionRelapse::class, 'addiction_id', 'id')->where('type_id', RelapseType::FULL_RELAPSE);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    public function usage()
    {
        $carbon = Carbon::now();

        switch($this->moderated_date_format_id) {
            case DateFormat::MINUTE:
                $carbon->subMinutes($this->moderated_amount);
                break;
            
            case DateFormat::HOUR:
                $carbon->subHours($this->moderated_amount);
                break;

            case DateFormat::DAY:
                $carbon->subDays($this->moderated_amount);
                break;

            case DateFormat::MONTH:
                $carbon->subMonths($this->moderated_amount);
                break;

            case DateFormat::YEAR:
                $carbon->subYears($this->moderated_amount);
                break;
        }

        return
            $this->hasMany(AddictionRelapse::class, 'addiction_id', 'id')
                ->where('type_id', RelapseType::MODERATED_USE)
                ->where('created_at', '>=', $carbon->toDatetimeString());
    }

    public function getStartCarbon()
    {
        $relapses = $this->relapses()->get();

        if ($relapses->count() > 0) {
            $carbon = Carbon::parse($relapses->last()->created_at);
        } else {
            $created_at = Carbon::parse($this->created_at);
            $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $this->start_date . $created_at->format(' H:i:s'));
        }

        return $carbon;
    }

    public function getElapsedCarbon()
    {
        $carbon = $this->getStartCarbon();

        return Carbon::now()->diff($carbon);
    }
}
