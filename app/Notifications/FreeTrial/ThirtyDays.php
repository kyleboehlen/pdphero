<?php

namespace App\Notifications\FreeTrial;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ThirtyDays extends Notification
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
                    ->subject('Halfway through your free trial already!?')
                    ->greeting('Hey there!')
                    ->line('Looks like you have 30 days left in your free trial of PDPHero. Hopefully it has been helping you get on track to achieving your wildest dreams and ambitions!')
                    ->line('If there is anything you need help with, or anything we can do to make your experience better don\'t hesitate to reach out to us at support@pdphero.com or submit a support request within the app.')
                    ->line('If you\'ve decided PDPHero brings you value and you\'d like to continue using it, go ahead and subscribe so your service isn\'t interrupted. Don\'t worry, you\'ll still keep the 30 days left in your trial and won\'t be billed until it\'s over :)')
                    ->line('You can subscribe by clicking the link, or go into your profile and click \'Manage Membership\'')
                    ->action('Subscribe', route('stripe'))
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
