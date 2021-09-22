<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class SMSLimit extends Model
{
    protected $table = 'sms_limits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'month', 'year',
    ];
}
