<?php

namespace App\Notifications\Addictions;

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

class MilestoneReached extends Notification
{
    use Queueable, SerializesModels;

    public $milestone;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($milestone)
    {
        $this->milestone = $milestone;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Get notification channel array
        $this->milestone->load('addiction');
        $this->milestone->addiction->load('user');
        $notification_channel = $this->milestone->addiction->user->getNotificationChannel();

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
        $milestone = $this->milestone;
        $milestone->load('addiction');
        $addiction = $milestone->addiction;

        $mail_message = 
            (new MailMessage)
                ->subject('PDPHero Addiction Milestone Reached')
                ->greeting('Hey there!')
                ->line("I'm pleased to let you know you've reached your milestone of $milestone->name on your $addiction->name addiction!");

        if(!is_null($milestone->reward))
        {
            $mail_message = $mail_message->line("It is time to reward yourself with $milestone->reward.");
        }

        $mail_message =
            $mail_message
                ->action('View Addiction', route('addiction.details', ['addiction' => $addiction->uuid]))
                ->line('Congratulations, we\'re so proud of you :)');

        return $mail_message;
    }

    public function toVonage($notifiable)
    {
        $milestone = $this->milestone;
        $milestone->load('addiction');
        $addiction = $milestone->addiction;

        $content = "Congratulations on reaching your milestone of $milestone->name on your $addiction->name addiction!";

        if(!is_null($milestone->reward))
        {
            $content .= " It's time to reward yourself with $milestone->reward :)";
        }

        return (new VonageMessage)
            ->content($content);
    }

    public function toWebPush($notifiable, $notification)
    {
        $milestone = $this->milestone;
        $milestone->load('addiction');
        $addiction = $milestone->addiction;

        $body = "Congratulations on reaching your milestone of $milestone->name on your $addiction->name addiction!";

        if(!is_null($milestone->reward))
        {
            $body .= " It's time to reward yourself with $milestone->reward :)";
        }

        return (new WebPushMessage)
            ->title('PDPHero Addiction Milestone Reached')
            ->icon(asset('logos/logo-black.png'))
            ->body($body);
    }
}
