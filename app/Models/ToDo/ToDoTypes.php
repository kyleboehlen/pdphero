<?php

namespace App\Models\ToDo;

use Illuminate\Database\Eloquent\Model;

class ToDoTypes extends Model
{
    public $timestamps = false;

    protected $fillable = ['id', 'name', 'style_class', ];
}
