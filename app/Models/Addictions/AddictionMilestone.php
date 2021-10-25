<?php

namespace App\Models\Addictions;

use Illuminate\Database\Eloquent\Model;
use JamesMills\Uuid\HasUuidTrait;

// Models
use App\Models\Addictions\Addiction;

class AddictionMilestone extends Model
{
    use HasUuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'addiction_id', 'name', 'amount', 'date_format_id', 'reward'
    ];

    // Relationships
    public function addiction()
    {
        return $this->hasOne(Addiction::class, 'id', 'addiction_id');
    }
}
