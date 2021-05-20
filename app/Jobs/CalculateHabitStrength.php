<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// Models
use App\Models\Habits\Habits;

class CalculateHabitStrength implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $habit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Habits $habit)
    {
        $this->habit = $habit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->habit->calculateStrength();
    }
}
