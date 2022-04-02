<?php

namespace App\Models\Goal;

use Illuminate\Database\Eloquent\Model;
use KyleBoehlen\Uuid\HasUuidTrait;
use Carbon\Carbon;

// Models
use App\Models\Goal\GoalActionItem;

class GoalActionItemReminder extends Model
{
    use HasUuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'action_item_id', 'remind_at',
    ];

    /**
     * Get formatted version of a goal action item remind at
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

    // Action item relationship
    public function actionItem()
    {
        return $this->hasOne(GoalActionItem::class, 'id', 'action_item_id');
    }
}
