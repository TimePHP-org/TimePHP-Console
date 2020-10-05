<?php

namespace App\Commands\Migration;

use Illuminate\Console\Scheduling\Schedule;
use App\Traits\Foundation;
use LaravelZero\Framework\Commands\Command;

class Down extends Command
{

    use Foundation;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'migration:down {--table= : Name of the table you want to delete in the database}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete the table in the database';

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
            $table = $this->ask("Which table do you want to delete");
        }

        $migration = ucfirst(strtolower($table))."Migration";

        $migrationNamespace = "\App\Migration\\$migration";

        if(class_exists($migrationNamespace) && is_callable([new $migrationNamespace(), "down"])){
            call_user_func([new $migrationNamespace(), "down"]);
            $this->line("Table deleted successfully <info>OK !</info>");
        } else {
            $this->line("<error>Error !</error> This migration doesn't exist");
        }


        
    }

}
