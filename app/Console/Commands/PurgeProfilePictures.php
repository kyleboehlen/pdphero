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
        // Get silent option
        $silent = $this->option('silent');

        if (!$silent) {
            echo "Purging stale profile pictures...\n";
        }

        // Get all the file names of the current profile pictures
        $filenames = User::whereNotNull('profile_picture')->get()->pluck('profile_picture')->toArray();

        // Get all the files in the profile pictures dir
        $files = Storage::files('profile-pictures');

        $deletes = 0; // Delete counter
        foreach($files as $file)
        {
            $name = str_replace('profile-pictures/', '', $file); // Remove directory from file name
            if(!in_array($name, $filenames)) // Check if file name is in array of current profile pictures
            {
                if(Storage::delete($file)) // Delete stale file
                {
                    $deletes++;
                }
                else
                {
                    // Print errror
                    if(!$silent)
                    {
                        echo "Failed to delete stale profile picture $name\n";
                    }
                }
            }
        }

        // Log/print completion
        if (!$silent) {
            echo "Completed! Purged $deletes stale profile pictures.\n";
        }

        return 0;
    }
}
