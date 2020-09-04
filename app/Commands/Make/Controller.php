<?php

namespace App\Commands\Make;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class Controller extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'make:controller {name : Name of the controller}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a controller';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $argName = strtolower($this->argument('name'));
        $tmpName = str_replace("controller", "", $argName);

        if(empty($tmpName)) {
            $this->error("Invalid controller name");
        } else {
            $controllerName = ucfirst($tmpName) . "Controller";

            if(file_exists($controllerName.".php")) {
                $this->error("This Controller already exists");
            } else {                
                $controllerContent = str_replace("%Controller%", $controllerName, file_get_contents(getcwd() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "Templates" . DIRECTORY_SEPARATOR . "Controller.template"));
                
                File::put(getcwd() . DIRECTORY_SEPARATOR . $controllerName . ".php", $controllerContent);
                $this->info("Done!");
            }
        }
    }
}
