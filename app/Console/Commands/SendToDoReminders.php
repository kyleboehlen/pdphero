<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

// Models
use App\Models\ToDo\ToDo;
use App\Models\User\User;

// Notifications
use App\Notifications\ToDo\Reminder as ToDoNotification;

class SendToDoReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:to-do';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks and sends notifications for any todo item reminders.';

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
        $now = Carbon::now()->toDatetimeString();
        $to_dos = ToDo::whereHas('reminders', function ($q) use ($now){
            $q->where('remind_at', '<=', $now);
        })->with('reminders', function($q) use ($now){
            $q->where('remind_at', '<=', $now);
        })->get();

        foreach($to_dos as $to_do)
        {
            $user = User::find($to_do->user_id);

            foreach($to_do->reminders as $reminder)
            {
                $user->notify(new ToDoNotification($to_do));
                
                // Delete reminder
                if(!$reminder->delete())
                {
                    Log::error('Failed to delete todo reminder after sending notification in send to do reminders job', $reminder->toArray());
                }
            }
        }
    }
}
