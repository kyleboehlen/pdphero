<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Log;

// Models
use App\Models\User\User;

class PurgeProfilePictures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purge:profile-pictures  {--silent}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will delete all profile pictures that are no longer in use.';

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
        // Set command start time stamp
        $carbon = Carbon::now();

        // Get silent option
        $silent = $this->option('silent');

        // Get all the file names of the current profile pictures
        $filenames = User::whereNotNull('profile_picture')->get()->pluck('profile_picture')->toArray();

        // Add .gitignore
        array_push($filenames, '.gitignore');

        // Get all the files in the profile pictures dir
        $files = Storage::files('public/profile-pictures');

        $deletes = 0; // Delete counter
        foreach($files as $file)
        {
            $name = str_replace('public/profile-pictures/', '', $file); // Remove directory from file name
            if(!in_array($name, $filenames)) // Check if file name is in array of current profile pictures
            {
                if(Storage::delete($file)) // Delete stale file
                {
                    $deletes++;
                }
                else
                {
                    // Log/print errror
                    $message = "Failed to delete stale profile picture $name";
                    Log::warning($message);
                    if(!$silent)
                    {
                        echo $message . "\n";
                    }
                }
            }
        }

        // Calculate script runtime from carbon created at script start
        $runtime_seconds = Carbon::now()->diffInSeconds($carbon);

        // Log/print completion
        $message = "purge:profile-pictures deleted $deletes stale files";
        Log::notice($message, ['runtime_in_seconds' => $runtime_seconds]);
        if(!$silent)
        {
            echo $message . " in $runtime_seconds seconds\n";
        }

        return 0;
    }
}
