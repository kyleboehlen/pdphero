<?php

namespace App\Notifications\ToDo;

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

    public $todo;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($todo)
    {
        $this->todo = $todo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $user = User::find($this->todo->user_id);
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
        $todo = $this->todo;
        return (new MailMessage)
            ->subject('PDPHero To-Do Item Reminder')
            ->greeting('Hey there!')
            ->line("Here is your reminder for your $todo->title to-do item.")
            ->action('To-Do List', route('todo.list'))
            ->line('Thank you for using PDPHero, have a wonderful day :)');
    }

    public function toNexmo($notifiable)
    {
        $todo = $this->todo;
        return (new NexmoMessage)
            ->content("Reminder to do $todo->title in PDPHero!");
    }

    public function toWebPush($notifiable, $notification)
    {
        $todo = $this->todo;
        return (new WebPushMessage)
            ->title('PDPHero To-Do Reminder')
            ->icon(asset('logos/logo-black.png'))
            ->body("Reminder to do $todo->title in PDPHero!");
    }
}
