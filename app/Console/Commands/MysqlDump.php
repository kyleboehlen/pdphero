<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;

class MysqlDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mysql:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command dumps the mysql database to app storage.';

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
        $host = config('database.connections.mysql.host');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $file = storage_path("mysqldump/$database.sql");

        $cmd = "mysqldump --no-tablespaces -h $host -u $username --password=\"$password\" $database > $file";
        exec($cmd);
        
        Log::info('Finished mysqldump.');
    }
}
