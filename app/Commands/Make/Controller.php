<?php

namespace App\Commands\Make;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use App\Traits\Foundation;

class Controller extends Command {

   use Foundation;

   /**
    * The signature of the command.
    *
    * @var string
    */
   protected $signature = 'make:controller {--name= : Name of the controller}';

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
   public function handle() {
      
      $name = $this->option('name');
      while ($name === null || empty($name)) {
         $name = $this->ask("Enter the name of the controller");
      }

      $argName = strtolower($name);
      $tmpName = str_replace("controller", "", $argName);

      if (empty($tmpName)) {
         $this->line("Invalid controller name : <error>FAILED !</error>");
      } else {
         $controllerName = ucfirst($tmpName) . "Controller";

         if (file_exists($this->getControllerPath() . DIRECTORY_SEPARATOR . $controllerName . ".php")) {
            $this->line("This controller already exists : <error>FAILED !</error>");
         } else {
            $controllerContent = str_replace("%Controller%", $controllerName, File::get($this->getTemplatePath() . DIRECTORY_SEPARATOR . "Controller.template"));

            File::put($this->getControllerPath() . DIRECTORY_SEPARATOR . $controllerName . ".php", $controllerContent);
            $this->line("Controller created successfully : <info>OK !</info>");
         }
      }
   }
}
