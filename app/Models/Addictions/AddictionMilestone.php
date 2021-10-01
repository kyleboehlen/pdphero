<?php

namespace App\Models\Addictions;

use Illuminate\Database\Eloquent\Model;
use JamesMills\Uuid\HasUuidTrait;

class AddictionMilestone extends Model
{
    use HasUuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'addiction_id', 'name', 'amount', 'date_format',
    ];
}
