<?php

namespace App\Models\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Constants
use App\Helpers\Constants\ToDo\Type as ToDoType;

// Models
use App\Models\Affirmations\Affirmations;
use App\Models\Affirmations\AffirmationsReadLog;
use App\Models\Habits\Habits;
use App\Models\ToDo\ToDo;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'values' => 'array',
    ];


    /**
     * Returns the value set for the user
     * of the specified setting
     *
     * @return string
     */
    public function getSettingValue($setting_id)
    {
        // Check if user has the setting saved
        $user_setting = UsersSettings::where('user_id', $this->id)->where('setting_id', $setting_id)->first();

        if(is_null($user_setting)) // If they don't have that setting
        {
            // Return the default value for that setting
            $default = config('settings.default');
            return $default[$setting_id];
        }

        // Return the value for that user's setting
        return $user_setting->value;
    }

    // Relationships
    public function affirmations()
    {
        return $this->hasMany(Affirmations::class, 'user_id', 'id')->orderBy('created_at');
    }

    public function affirmationsRead()
    {
        return $this->hasMany(Affirmations::class, 'user_id', 'id')->orderBy('updated_at');
    }

    public function affirmationsReadLog()
    {
        return $this->hasMany(AffirmationsReadLog::class, 'user_id', 'id');
    }

    public function todos()
    {
        return $this->hasMany(ToDo::class, 'user_id', 'id');
    }

    public function completedTodos()
    {
        return $this->hasMany(ToDo::class, 'user_id', 'id')->where('type_id', ToDoType::TODO_ITEM)->where('completed', 1);
    }

    public function completedHabits()
    {
        return $this->hasMany(Habits::class, 'user_id', 'id')->where('strength', 100);
    }
}
