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

            if(file_exists($this->getControllerPath().$controllerName.".php")) {
                $this->error("This Controller already exists");
            } else {                
                $controllerContent = str_replace("%Controller%", $controllerName, File::get($this->getTemplatePath()."Controller.template"));
                
                File::put($this->getControllerPath().$controllerName . ".php", $controllerContent);
                $this->task("", fn() => true );
            }
        }
    }

    /**
     * Get controllers path
     *
     * @return string
     */
    private function getControllerPath(): string
    {
        return getcwd().DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."App".DIRECTORY_SEPARATOR."Bundle".DIRECTORY_SEPARATOR."Controllers".DIRECTORY_SEPARATOR.PHP_EOL;
    }

    /**
     * Get templates path
     *
     * @return string
     */
    private function getTemplatePath(): string 
    {
        return getcwd().DIRECTORY_SEPARATOR."bin".DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR.PHP_EOL;
    }
}
