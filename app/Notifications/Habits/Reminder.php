<?php

namespace App\Notifications\Habits;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Queue\SerializesModels;

// Constants
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\User\User;

class Reminder extends Notification
{
    use Queueable, SerializesModels;

    public $habit;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($habit)
    {
        $this->habit = $habit;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $user = User::find($this->habit->user_id);
        $notification_channel = $user->getSettingValue(Setting::NOTIFICATION_CHANNEL);

        switch($notification_channel)
        {
            case Setting::NOTIFICATION_EMAIL:
                return ['mail'];
                break;

            case Setting::NOTIFICATION_SMS:
                return ['nexmo'];
                break;

            case Setting::NOTIFICATION_WEBPUSH:
                return [WebPushChannel::class];
                break;

            default:
                return null;
                break;
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $habit = $this->habit;
        return (new MailMessage)
            ->subject('PDPHero Habit Reminder')
            ->greeting('Hey there!')
            ->line("Here is your reminder for your $habit->name habit.")
            ->action('View Habit', route('habits.view', ['habit' => $habit->uuid]))
            ->line('Thank you for using PDPHero, have a wonderful day :)');
    }

    public function toNexmo($notifiable)
    {
        $habit = $this->habit;
        return (new NexmoMessage)
            ->content("Reminder to perform your $habit->name habit in PDPHero!");
    }

    public function toWebPush($notifiable, $notification)
    {
        $habit = $this->habit;
        return (new WebPushMessage)
            ->title('PDPHero Habit Reminder')
            ->icon(asset('logos/logo-black.png'))
            ->body("Reminder to perform your $habit->name habit in PDPHero!");
    }
}