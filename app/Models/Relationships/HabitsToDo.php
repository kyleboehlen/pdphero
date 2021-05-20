<?php

namespace App\Models\Relationships;

use Illuminate\Database\Eloquent\Model;

class HabitsToDo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'habits_id', 'to_do_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'habits_to_do';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
