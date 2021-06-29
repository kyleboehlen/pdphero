<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class MysqlPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mysql:passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets all of the passwords in the database.';

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
        DB::table('users')->update(['password' => '', 'remember_token' => null]);
    }
}
