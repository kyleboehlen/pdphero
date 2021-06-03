<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Log;

// Models
use App\Models\User\User;

// Notifications
use App\Notifications\FreeTrial\ThirtyDays;
use App\Notifications\FreeTrial\ThreeDays;

class FreeTrialSendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'free-trial:send-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will check the days left in all user\'s free trials and send applicable notifications.';

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
        $users = User::all();

        $thirty_days = 0;
        $three_days = 0;

        foreach($users as $user)
        {
            $trial_days_left = $user->getTrialDaysLeft();

            if($trial_days_left == 30)
            {
                $user->notify(new ThirtyDays());
                $thirty_days++;
            }
            elseif($trial_days_left == 3)
            {
                $user->notify(new ThreeDays());
                $three_days++;
            }
        }

        // Log completion
        Log::notice('Finished sending free trial notifications.', [
            'thirty_days' => $thirty_days,
            'three_days' => $three_days,
        ]);

        return 0;
    }
}
