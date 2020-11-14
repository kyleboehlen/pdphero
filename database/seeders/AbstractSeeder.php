<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

abstract class AbstractSeeder extends Seeder
{
    public $config;
    public $failures = 0;
    public $model;

    abstract public function failure($item);

    final public function __construct()
    {
        $this->config = static::CONFIG;
        $this->model = static::MODEL;
    }

    public function printFailures()
    {
        if($this->failures > 0)
        {
            // Formatted to look good in the console output of db:seed
            echo "\e[31mFailures:\e[0m see error log for details ($failures failed)\n";
        }
    }

    public function run()
    {
        foreach(config($this->config) as $id => $item)
        {
            $seed = $this->model::find($id);
            
            if(is_null($seed))
            {
                $seed = new $this->model;
                $item['id'] = $id; // Add id to the array so it fills
            }
            
            $seed->fill($item);

            if(!$seed->save())
            {
                $this->failures++;

                $this->failure($item);
            }
        }

        $this->printFailures();
    }
}