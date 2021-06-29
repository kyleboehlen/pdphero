<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;

class MysqlImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mysql:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports a mysqldump file.';

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
        $file = storage_path("mysqldump/pdphero.sql");

        $cmd = "mysql -h $host -u $username --password=\"$password\" $database < $file";
        exec($cmd);
        
        Log::info('Finished mysql import.');
    }
}
