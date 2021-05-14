<?php

namespace App\Models\Habits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Models
use App\Models\Habits\Habits;

class HabitHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'habit_id', 'type_id', 'day', 'times', 'notes',
    ];

    public function habit()
    {
        return $this->hasOne(Habits::class, 'id', 'habit_id');
    }
}
