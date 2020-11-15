<?php

namespace App\Models\Todo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\Uuid\HasUuidTrait;
use Log;

// Models
use App\Models\ToDo\ToDoPriority;

class ToDo extends Model
{
    use HasFactory, HasUuidTrait, SoftDeletes;

    public function priority()
    {
        return $this->hasOne(ToDoPriority::class, 'id', 'priority_id');
    }

    /**
     * Toggles the completed attribute and
     * returns the success of saving the model
     *
     * @return bool
     */
    public function toggleCompleted()
    {
        // Toggle completed status
        $this->completed = !$this->completed;

        // Save model
        if(!$this->save())
        {
            // Log error return failure
            Log::error('Failed to toggle completed on to-do item', ['uuid' => $this->uuid]);
            return false;
        }

        // Rerturn success
        return true;
    }
}
