<?php

namespace App\Models\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

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
}
