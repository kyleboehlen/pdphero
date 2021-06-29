<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;

class MysqlSftp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mysql:scp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copies the mysql dump from a production server by sftp.';

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
        $ip = $this->ask('IP: ');
        $username = $this->ask('Username: ');
        $path = storage_path('mysqldump/');
        $file = $path . 'pdphero.sql';

        $cmd = "scp $username@$ip:$file $file";
        exec($cmd);
        
        Log::info('Finished mysql import.');
    }
}
