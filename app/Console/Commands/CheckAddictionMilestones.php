<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;

// Constants
use App\Helpers\Constants\Addiction\DateFormat;
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\Addictions\Addiction;

// Notifications
use App\Notifications\Addictions\MilestoneReached;

class CheckAddictionMilestones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:addiction-milestones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the milestones of all addictions to see if they have been reached.';

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
        $addictions = Addiction::with('pendingMilestones')->with('user')->get();

        foreach($addictions as $addiction)
        {
            $carbon_start = $addiction->getStartCarbon();
            $carbon_now = Carbon::now();
            
            foreach($addiction->pendingMilestones as $milestone)
            {
                $carbon_milestone = clone $carbon_start;

                switch($milestone->date_format_id)
                {
                    case DateFormat::MINUTE:
                        $carbon_milestone->addMinutes($milestone->amount);
                        break;
                    
                    case DateFormat::HOUR:
                        $carbon_milestone->addHours($milestone->amount);
                        break;
        
                    case DateFormat::DAY:
                        $carbon_milestone->addDays($milestone->amount);
                        break;
        
                    case DateFormat::MONTH:
                        $carbon_milestone->addMonths($milestone->amount);
                        break;
        
                    case DateFormat::YEAR:
                        $carbon_milestone->addYears($milestone->amount);
                        break;
                }

                if($carbon_milestone->lessThanOrEqualTo($carbon_now))
                {
                    $milestone->reached = true;

                    if($milestone->save())
                    {
                        // See if we're supposed to send a notification
                        if($addiction->user->getSettingValue(Setting::SEND_ADDICTION_MILESTONE_NOTIFICATIONS))
                        {
                            $addiction->user->notify(new MilestoneReached($milestone));
                        }
                    }
                    else
                    {
                        Log::error('Failed to set addiction milestone as reached', [
                            'milestone' => $milestone->toArray(),
                        ]);
                    }
                }
            }
        }
    }
}
