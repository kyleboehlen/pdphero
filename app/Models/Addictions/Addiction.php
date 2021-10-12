<?php

namespace App\Models\Addictions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;

// Constants
use App\Helpers\Constants\Addiction\RelapseType;

// Models
use App\Models\Addictions\AddictionMilestone;
use App\Models\Addictions\AddictionRelpase;

class Addiction extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'method_id', 'details', 'start_date',
    ];


    // Relationships
    public function milestones()
    {
        return $this->hasMany(AddictionMilestone::class, 'addiction_id', 'id')->orderBy('days');
    }

    public function relapses()
    {
        return $this->hasMany(AddictionRelapse::class, 'addiction_id', 'id')->where('type_id', RelapseType::FULL_RELAPSE);
    }
}
