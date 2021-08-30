<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

// Models
use App\Models\Goal\GoalActionItem;
use App\Models\User\User;

// Notifications
use App\Notifications\Goals\ActionItemReminder as ActionItemNotification;

class SendGoalActionItemReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:goal-action-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks and sends notifications for any goal action item reminders.';

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
        $action_items = GoalActionItem::whereHas('reminders', function ($q) use ($now){
            $q->where('remind_at', '<=', $now);
        })->with('reminders', function($q) use ($now){
            $q->where('remind_at', '<=', $now);
        })->get();

        foreach($action_items as $action_item)
        {
            $action_item->load('goal');
            $user = User::find($action_item->goal->user_id);

            foreach($action_item->reminders as $reminder)
            {
                $user->notify(new ActionItemNotification($action_item));
                
                // Delete reminder
                if(!$reminder->delete())
                {
                    Log::error('Failed to delete goals action item reminder after sending notification in send goal action item reminders job', $reminder->toArray());
                }
            }
        }
    }
}
