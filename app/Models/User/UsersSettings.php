<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersSettings extends Model
{
    use HasFactory;

    public $incrementing = false;
    
    public $fillable = [
        'user_id', 'setting_id', 'value',
    ];
}
