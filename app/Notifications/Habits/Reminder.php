<?php

namespace App\Notifications\Habits;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
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
        // Get user
        $user = User::find($this->habit->user_id);

        // Get notification channel array
        $notification_channel = $user->getNotificationChannel();

        return $notification_channel;
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

    public function toVonage($notifiable)
    {
        $habit = $this->habit;
        return (new VonageMessage)
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
