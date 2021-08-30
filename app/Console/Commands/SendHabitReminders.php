<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

// Models
use App\Models\Habits\Habits;
use App\Models\User\User;

// Notifications
use App\Notifications\Habits\Reminder as HabitNotification;

class SendHabitReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:habits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks and sends notifications for any habit reminders.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $now = Carbon::now();
        $habits = Habits::all();

        foreach($habits as $habit)
        {
            $habit->load('reminders');
            if($habit->reminders->count() > 0)
            {
                $user = User::find($habit->user_id);
                $timezone = $user->timezone ?? 'America/Denver';
                $now->setTimezone($timezone);
                
                if($habit->notificationRequired($now))
                {
                    foreach($habit->reminders as $reminder)
                    {
                        if($now->format('H:i') . ':00' == $reminder->remind_at)
                        {
                            $user->notify(new HabitNotification($habit));
                        }
                    }
                }
            }
        }
    }
}
