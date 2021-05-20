<?php

namespace App\Models\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Constants
use App\Helpers\Constants\Habits\Type as HabitsType;
use App\Helpers\Constants\ToDo\Type as ToDoType;

// Models
use App\Models\Affirmations\Affirmations;
use App\Models\Affirmations\AffirmationsReadLog;
use App\Models\FirstVisit\FirstVisitMessages;
use App\Models\FirstVisit\FirstVisitDisplayed;
use App\Models\Goal\Goal;
use App\Models\Goal\GoalCategory;
use App\Models\Journal\JournalCategory;
use App\Models\Habits\Habits;
use App\Models\Relationships\UsersHideHome;
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
        'rules' => 'array',
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

    public function goals()
    {
        return $this->hasMany(Goal::class, 'user_id', 'id');
    }

    public function accomplishedGoals()
    {
        return $this->hasMany(Goal::class, 'user_id', 'id')->where('progress', '>=', 100)->where('achieved', 1);
    }

    public function goalCategories()
    {
        return $this->hasMany(GoalCategory::class, 'user_id', 'id')->orderBy('name');
    }

    public function todos()
    {
        return $this->hasMany(ToDo::class, 'user_id', 'id');
    }

    public function completedTodos()
    {
        return $this->hasMany(ToDo::class, 'user_id', 'id')->where('type_id', ToDoType::TODO_ITEM)->where('completed', 1);
    }

    public function habits()
    {
        return $this->hasMany(Habits::class, 'user_id', 'id')->where('type_id', HabitsType::USER_GENERATED)->orderBy('name');
    }

    public function completedHabits()
    {
        return $this->hasMany(Habits::class, 'user_id', 'id')->where('strength', 100);
    }

    public function journalCategories()
    {
        return $this->hasMany(JournalCategory::class, 'user_id', 'id')->orderBy('name');
    }

    public function firstVisitMessage($route_name)
    {
        return
            FirstVisitMessages::whereNotIn('id',
                $this->firstVisitDisplayed()->pluck('message_id')->toArray()
            )->where('route_name', $route_name)->first();
    }

    public function firstVisitDisplayed()
    {
        return $this->hasMany(FirstVisitDisplayed::class, 'user_id', 'id');
    }

    public function hideHomeArray()
    {
        return $this->hasMany(UsersHideHome::class, 'user_id', 'id')->get()->pluck('home_id')->toArray();
    }
}