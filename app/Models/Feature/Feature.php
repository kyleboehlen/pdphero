<?php

namespace App\Models\Feature;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;

// Models
use App\Models\Feature\FeatureVote;

class Feature extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    public function calculateScore()
    {
        $this->load('votes');
        $this->score = 0;
        foreach($this->votes as $vote)
        {
            $this->score += $vote->value;
        }

        return $this->save();
    }

    public function votes()
    {
        return $this->hasMany(FeatureVote::class, 'feature_id', 'id');
    }
}
