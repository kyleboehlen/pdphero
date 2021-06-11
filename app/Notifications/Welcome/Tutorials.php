<?php

namespace App\Notifications\Welcome;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Tutorials extends Notification
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
        return (new MailMessage)
                    ->subject('Setting Up PDPHero')
                    ->greeting('Welcome to PDPHero!')
                    ->line('Looks like you just started a free trial and are on your way to taking control of your personal development planning. We highly recommend checking out our tutorials so you know everything that is available to you in the app :)')
                    ->line('After you\'ve checked out the tutorials if there are any additional questions you have don\'t hesitate to reach out to us at support@pdphero.com or submit a support request within the app.')
                    ->action('Tutorials', route('tutorials'))
                    ->line('Thank you for giving PDPHero a try, and may you accomplish your wildest dreams!');
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
