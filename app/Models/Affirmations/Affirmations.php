<?php

namespace App\Models\Affirmations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;

class Affirmations extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;
}
