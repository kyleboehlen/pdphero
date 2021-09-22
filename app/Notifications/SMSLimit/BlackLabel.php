<?php

namespace App\Notifications\SMSLimit;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BlackLabel extends Notification
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
        $black_label_limit = config('sms.black_label_limit');
        return (new MailMessage)
            ->subject('SMS Limit Reached')
            ->greeting('Hey there!')
            ->line("You have reached your SMS limit of $black_label_limit notifications. Looks like you've been busy!")
            ->line('Your limit will reset on the first of the month, until then you can change your notification settings to email or webpush.')
            ->action('Settings', route('stripe'))
            ->line('If you have any issues with this SMS limitation please reach out to us at support@pdphero.com :)')
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
