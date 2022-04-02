<?php

namespace App\Models\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Laravel\Cashier\Billable; // Stripe
use Carbon\Carbon;
use Log;

// Constants
use App\Helpers\Constants\User\Setting;
use App\Helpers\Constants\Habits\Type as HabitsType;
use App\Helpers\Constants\ToDo\Type as ToDoType;

// Models
use App\Models\Addictions\Addiction;
use App\Models\Affirmations\Affirmations;
use App\Models\Affirmations\AffirmationsReadLog;
use App\Models\Bucketlist\BucketlistCategory;
use App\Models\FirstVisit\FirstVisitMessages;
use App\Models\FirstVisit\FirstVisitDisplayed;
use App\Models\Goal\Goal;
use App\Models\Goal\GoalCategory;
use App\Models\Journal\JournalCategory;
use App\Models\Journal\JournalEntry;
use App\Models\Habits\Habits;
use App\Models\Relationships\UsersHideHome;
use App\Models\ToDo\ToDo;
use App\Models\ToDo\ToDoCategory;
use App\Models\User\SMSLimits;

// Notifications
use App\Notifications\SMSLimit\Basic;
use App\Notifications\SMSLimit\BlackLabel;
use App\Notifications\SMSLimit\Trial;

// Notification Channels
use NotificationChannels\WebPush\WebPushChannel;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes,
        Billable, // Stripe
        HasPushSubscriptions; // Web Push notifications

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'refer_slug',
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
     * Route notifications for the Vonage channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForVonage($notification)
    {
        return '1' . substr($this->sms_number, -10);
    }

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

    public function getTrialDaysLeft()
    {
        $timezone = $this->timezone ?? 'America/Denver';
        $carbon = Carbon::parse($this->created_at)->setTimezone($timezone);

        $diff_in_days = Carbon::now($timezone)->diffInDays($carbon);

        $trial_length = config('membership.trial_length');

        if(in_array($this->email, config('test.alpha.emails')))
        {
            $trial_length += config('test.alpha.trial_bonus');
        }

        if($diff_in_days < $trial_length)
        {
            return $trial_length - $diff_in_days;
        }

        return 0;
    }

    // Relationships
    public function addictions()
    {
        return $this->hasMany(Addiction::class, 'user_id', 'id');
    }

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

    public function bucketlistCategories()
    {
        return $this->hasMany(BucketlistCategory::class, 'user_id', 'id')->orderBy('name');
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

    public function todoCategories()
    {
        return $this->hasMany(ToDoCategory::class, 'user_id', 'id')->orderBy('name');
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

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'user_id', 'id');
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

    public function getNotificationChannel()
    {
        // Check memebership
        if($this->getTrialDaysLeft() == 0 && !$this->subscribed(config('membership.basic.slug')) && !$this->subscribed(config('membership.black_label.slug')))
        {
            return null;
        }

        // Return notification channel
        $notification_channel = $this->getSettingValue(Setting::NOTIFICATION_CHANNEL);
        switch($notification_channel)
        {
            case Setting::NOTIFICATION_EMAIL:
                return ['mail'];
                break;

            case Setting::NOTIFICATION_SMS:
                if(!$this->checkSMSLimit())
                {
                    return null;   
                }
                return ['vonage'];
                break;

            case Setting::NOTIFICATION_WEBPUSH:
                return [WebPushChannel::class];
                break;

            default:
                return null;
                break;
        }
    }

    private function checkSMSLimit()
    {
        $carbon = Carbon::now();
        $month = $carbon->format('n');
        $year = $carbon->format('Y');

        // Get/create SMS limit for the month
        $sms_limit = SMSLimit::where('user_id', $this->id)->where('month', $month)->where('year', $year)->first();
        if(is_null($sms_limit))
        {
            $data = [
                'user_id' => $this->id,
                'month' => $month,
                'year' => $year,
            ];

            $sms_limit = new SMSLimit($data);

            if(!$sms_limit->save())
            {
                Log::error('Failed to create SMSLimits model when checking user SMS limits', $data);
                return true;
            }
        }

        // Check user membership
        if($this->subscribed(config('membership.basic.slug')))
        {
            // Check if user has upgraded
            if($this->subscription(config('membership.basic.slug'))->stripe_plan == config('membership.black_label.stripe_price_id'))
            {
                $this->subscription(config('membership.basic.slug'))->update(['name' => config('membership.black_label.slug')]);
                return $this->checkSMSLimit();
            }

            $max_sent = config('sms.basic_limit');
            $notification = new Basic();
            $notify_property = 'notify_basic';
        }
        elseif($this->subscribed(config('membership.black_label.slug')))
        {
            $max_sent = config('sms.black_label_limit');
            $notification = new BlackLabel();
            $notify_property = 'notify_black_label';
        }
        elseif($this->getTrialDaysLeft() > 0)
        {
            $max_sent = config('sms.trial_limit');
            $notification = new Trial();
            $notify_property = 'notify_trial';
        }

        if($sms_limit->sent >= $max_sent)
        {
            if(!$sms_limit->$notify_property)
            {
                $this->notify($notification);

                $sms_limit->$notify_property = true;
                if(!$sms_limit->save())
                {
                    $data = [
                        'user_id' => $this->id,
                        'month' => $month,
                        'year' => $year,
                    ];
        
                    Log::error("Failed to set SMSLimit $notify_property sent", $data);
                }
            }

            return false;
        }

        $sms_limit->sent = $sms_limit->sent + 1;
        if(!$sms_limit->save())
        {
            $data = [
                'user_id' => $this->id,
                'month' => $month,
                'year' => $year,
            ];

            Log::error('Failed to increment SMSLimit sent', $data);
        }

        return true;
    }
}
