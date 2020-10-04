<?php

namespace App\Commands\Migration;

use Illuminate\Console\Scheduling\Schedule;
use App\Traits\Foundation;
use LaravelZero\Framework\Commands\Command;

class Up extends Command
{

    use Foundation;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'migration:up {--table= : Name of the table you want to create in the database}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create the table in the database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        require $this->getRootDirectory()."/vendor/autoload.php";

        $table = $this->option('table');
        while ($table === null || empty($table)) {
            $table = $this->ask("Which table do you want to create");
        }

        $migration = ucfirst(strtolower($table))."Migration";

        $migrationNamespace = "\App\Migration\\$migration";

        if(class_exists($migrationNamespace) && is_callable([new $migrationNamespace(), "up"])){
            call_user_func([new $migrationNamespace(), "up"]);
            $this->line("Table created successfully <info>OK !</info>");
        } else {
            $this->line("<error>Error !</error> This migration doesn't exist");
        }


        
    }

}
