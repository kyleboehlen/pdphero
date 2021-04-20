<?php

namespace App\Models\Goal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;

// Models
use App\Models\Goal\Goal;

class GoalActionItem extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    protected $fillable = [
        'goal_id', 'name', 'notes',
    ];

    public function goal()
    {
        return $this->hasOne(Goal::class, 'id', 'goal_id');
    }
}
