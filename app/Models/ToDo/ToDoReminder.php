<?php

namespace App\Models\ToDo;

use Illuminate\Database\Eloquent\Model;
use JamesMills\Uuid\HasUuidTrait;
use Carbon\Carbon;

// Models
use App\Models\ToDo\ToDo;

class ToDoReminder extends Model
{
    use HasUuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'to_do_id', 'remind_at',
    ];

    /**
     * Get formatted version of a todo remind at
     *
     * @param  string  $value
     * @return string
     */
    public function getRemindAtFormattedAttribute()
    {
        $timezone = \Auth::user()->timezone ?? 'America/Denver';
        $carbon = Carbon::parse($this->remind_at)->setTimezone('UTC')->setTimezone($timezone);

        return $carbon->format('D, M j @ g:i A');
    }

    // Todo item relationship
    public function todo()
    {
        return $this->hasOne(ToDo::class, 'id', 'to_do_id');
    }
}
