<?php

namespace App\Models\Habits;

use Illuminate\Database\Eloquent\Model;
use JamesMills\Uuid\HasUuidTrait;
use Carbon\Carbon;

// Models
use App\Models\Habits\Habits;

class HabitReminder extends Model
{
    use HasUuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'habit_id', 'remind_at',
    ];

    /**
     * Get formatted version of a habit remind at
     *
     * @param  string  $value
     * @return string
     */
    public function getRemindAtFormattedAttribute()
    {
        $carbon = Carbon::parse($this->remind_at);

        return 'On required days @ ' . $carbon->format('g:i A');
    }

    // Habit relationship
    public function habit()
    {
        return $this->hasOne(Habits::class, 'id', 'habit_id');
    }
}
