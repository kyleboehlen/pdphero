<?php

namespace App\Notifications\Goals;

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

class ActionItemReminder extends Notification
{
    use Queueable, SerializesModels;

    public $action_item;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($action_item)
    {
        $this->action_item = $action_item;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $this->action_item->load('goal');
        $user = User::find($this->action_item->goal->user_id);
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
        $action_item = $this->action_item;
        $action_item->load('goal');
        return (new MailMessage)
            ->subject('PDPHero Action Item Reminder')
            ->greeting('Hey there!')
            ->line("Here is your reminder for your $action_item->name action item.")
            ->action('View Goal', route('goals.view.goal', [
                'goal' => $action_item->goal->uuid,
                'selected-dropdown' => 'action-plan',
            ]))
            ->line('Thank you for using PDPHero, have a wonderful day :)');
    }

    public function toNexmo($notifiable)
    {
        $action_item = $this->action_item;
        return (new NexmoMessage)
            ->content("Reminder to achieve $action_item->name in PDPHero!");
    }

    public function toWebPush($notifiable, $notification)
    {
        $action_item = $this->action_item;
        return (new WebPushMessage)
            ->title('PDPHero Action Item Reminder')
            ->icon(asset('logos/logo-black.png'))
            ->body("Reminder to achieve $action_item->name in PDPHero!");
    }
}
