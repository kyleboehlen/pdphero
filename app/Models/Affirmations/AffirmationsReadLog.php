<?php

namespace App\Models\Affirmations;

use Illuminate\Database\Eloquent\Model;

class AffirmationsReadLog extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'read_at' => 'datetime',
    ];

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

    /**
     * Get the read_at attribute as a Y-m-d string
     *
     * @return string
     */
    public function getReadAtKeyAttribute()
    {
        return $this->read_at->format('Y-m-d');
}
}
