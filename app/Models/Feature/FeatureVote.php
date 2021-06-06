<?php

namespace App\Models\Feature;

use Illuminate\Database\Eloquent\Model;

class FeatureVote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'feature_id', 'user_id',
    ];
}
