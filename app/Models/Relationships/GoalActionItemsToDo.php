<?php

namespace App\Models\Relationships;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalActionItemsToDo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'action_item_id', 'to_do_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'goal_action_items_to_do';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
