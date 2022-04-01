<?php

namespace App\Models\Journal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KyleBoehlen\Uuid\HasUuidTrait;

class JournalCategory extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'name', 'user_id',
    ];
}
