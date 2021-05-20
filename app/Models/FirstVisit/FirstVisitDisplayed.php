<?php

namespace App\Models\FirstVisit;

use Illuminate\Database\Eloquent\Model;

class FirstVisitDisplayed extends Model
{
    protected $table = 'first_visit_displayed';

    protected $fillable = [
        'user_id', 'message_id',
    ];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->displayed_at = $model->freshTimestamp();
        });
    }
}
