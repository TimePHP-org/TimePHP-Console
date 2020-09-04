<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Test extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Test comand';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo File::get(getcwd()."/app/Templates/Controller.template");
    }
}
