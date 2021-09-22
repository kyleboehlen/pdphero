<?php

namespace App\Notifications\SMSLimit;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Trial extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $trial_limit = config('sms.trial_limit');
        $basic_limit = config('sms.basic_limit');
        $black_label_limit = config('sms.black_label_limit');
        return (new MailMessage)
            ->subject('SMS Limit Reached')
            ->greeting('Hey there!')
            ->line("You have reached your SMS limit of $trial_limit notifications.")
            ->line("Basic subscriptions have a limit of $basic_limit notifications and $black_label_limit with Black Label subscriptions.")
            ->line("Subscribe by clicking the link below, or go into your profile and click 'Manage Membership'.")
            ->action('Subscribe', route('stripe'))
            ->line('Otherwise, you can change your notification settings to email or webpush until your limit resets on the first of the month :)')
            ->line('Thank you for using our application, and have a wonderful day!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
