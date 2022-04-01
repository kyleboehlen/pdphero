<?php

namespace App\Models\Addictions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use KyleBoehlen\Uuid\HasUuidTrait;

class AddictionRelapse extends Model
{
    use HasFactory, HasUuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'addiction_id', 'type_id', 'notes',
    ];

}
