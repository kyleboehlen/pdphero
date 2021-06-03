<?php

namespace App\Notifications\FreeTrial;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ThreeDays extends Notification
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
                ->subject('Only three days left in your trial!')
                ->greeting('Hey there!')
                ->line('Looks like you have 3 days left in your free trial of PDPHero. Don\'t worry, if you can\'t subscibe just yet, or you aren\'t ready, we\'ll keep all your goals and habits safe for you :)')
                ->line('If there is anything you need help with, or if you have any feedback for us, don\'t hesitate to reach out to us at support@pdphero.com or submit a support request within the app.')
                ->line('If you\'ve decided PDPHero brings you value and you\'d like to continue using it, go ahead and subscribe so your service isn\'t interrupted.')
                ->line('You can subscribe by clicking the link, or go into your profile and click \'Manage Membership\'')
                ->action('Subscribe', route('stripe'))
                ->line('Thank you for trying PDPHero, and have a wonderful day!');
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
