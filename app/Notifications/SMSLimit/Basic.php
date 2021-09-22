<?php

namespace App\Notifications\SMSLimit;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Basic extends Notification
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
        $basic_limit = config('sms.basic_limit');
        $black_label_limit = config('sms.black_label_limit');
        return (new MailMessage)
            ->subject('SMS Limit Reached')
            ->greeting('Hey there!')
            ->line("You have reached your SMS limit of $basic_limit notifications.")
            ->line("If you'd like to up your limit to $lack_label_limit notificatons you can upgrade your membership to Black Label by clicking the link, or go into your profile and click 'Manage Membership'.")
            ->action('Upgrade', route('stripe'))
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
