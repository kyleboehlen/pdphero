<?php

namespace App\Models\Affirmations;

use Illuminate\Database\Eloquent\Model;

class AffirmationsReadLog extends Model
{
    protected $fillable = [
        'user_id',
    ];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->read_at = $model->freshTimestamp();
        });
    }
}
