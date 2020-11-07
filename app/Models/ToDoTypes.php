<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToDoTypes extends Model
{
    public $timestamps = false;

    protected $fillable = ['id', 'name', 'style_class', ];
}
