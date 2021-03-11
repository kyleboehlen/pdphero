<?php

namespace App\Models\Goal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;

// Models
use App\Models\Goal\GoalStatus;

class Goal extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    protected $fillable = [
        'name', 'reason', 'type_id', 'user_id', 'status_id',
    ];

    public function status()
    {
        return $this->hasOne(GoalStatus::class, 'id', 'status_id');
    }
}
