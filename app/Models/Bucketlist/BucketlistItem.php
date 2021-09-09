<?php

namespace App\Models\Bucketlist;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;
use Carbon\Carbon;

// Models
use App\Models\Bucketlist\BucketlistCategory;

class BucketlistItem extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'details', 'user_id',
    ];

    public function category()
    {
        return $this->hasOne(BucketlistCategory::class, 'id', 'category_id');
    }

    public function formattedCompletedAt()
    {
        return Carbon::parse($this->updated_at)->format('D, M jS Y, g:i A');
    }
}
