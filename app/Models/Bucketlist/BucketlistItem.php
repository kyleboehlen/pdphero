<?php

namespace App\Models\Bucketlist;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KyleBoehlen\Uuid\HasUuidTrait;
use Carbon\Carbon;

// Models
use App\Models\Bucketlist\BucketlistCategory;
use App\Models\Goal\Goal;

class BucketlistItem extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'notes', 'user_id',
    ];

    public function category()
    {
        return $this->hasOne(BucketlistCategory::class, 'id', 'category_id');
    }

    public function formattedCompletedAt()
    {
        $timezone = \Auth::user()->timezone ?? 'America/Denver';
        return Carbon::parse($this->updated_at)->setTimezone($timezone)->format('D, M jS Y, g:i A');
    }

    public function goal()
    {
        return $this->hasOne(Goal::class, 'id', 'goal_id');
    }
}
