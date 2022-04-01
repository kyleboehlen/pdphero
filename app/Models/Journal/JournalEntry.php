<?php

namespace App\Models\Journal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KyleBoehlen\Uuid\HasUuidTrait;

// Models
use App\Models\Journal\JournalCategory;

class JournalEntry extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    public function category()
    {
        return $this->hasOne(JournalCategory::class, 'id', 'category_id');
    }
}
