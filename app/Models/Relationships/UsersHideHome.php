<?php

namespace App\Models\Relationships;

use Illuminate\Database\Eloquent\Model;

class UsersHideHome extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'home_id',
    ];

    public $timestamps = false;
}
