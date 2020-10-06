<?php

namespace App\Commands\Make;

use App\Traits\Foundation;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class View extends Command {

   use Foundation;
   /**
    * The signature of the command.
    *
    * @var string
    */
   protected $signature = 'make:view {--name= : Name of the view}';

   /**
    * The description of the command.
    *
    * @var string
    */
   protected $description = 'Create a view';

   /**
    * Execute the console command.
    *
    * @return mixed
    */
   public function handle() {

      $path = $this->option('name'); // /home/home.twig
      while ($path === null || empty($path)) {
         $path = $this->ask("Enter the name of the view");
      }

      $path = str_replace(".twig", "", $path); // /home/home
      $path = trim($path, "/"); // home/home

      if ($path === null || empty($path)) {
			$this->line("<error>Error !</error> Invalid view name");
      } else {

			$pathArray = explode("/", $path); // ["home", "home"]
			
         if (file_exists($this->getViewPath() . implode("/", $pathArray) . ".twig")) {
            $this->line("<error>Error !</error> This view already exists");
         } else {
            $tmpPath = "";
            $cdPath = "";
            if (count($pathArray) > 1) {
               $cd = [];
               for ($i = 0; $i < count($pathArray) - 1; $i++) {
						if($i > 0){
							$tmpPath .= "/";
						}
                  $tmpPath .= $pathArray[$i];
                  $cd[] = "..";
               }

               if (!is_dir($this->getViewPath().$tmpPath)) {
                  mkdir($this->getViewPath().$tmpPath, 0777, true);
               }
					$cdPath = implode("/", $cd) . "/";
					$tmpPath = $tmpPath.DIRECTORY_SEPARATOR;
            }

				$name = end($pathArray); // home
				

            $viewTemplate = $this->getViewTemplate();
				$viewTemplate = str_replace("%layoutPath%", $cdPath, $viewTemplate);

            File::put($this->getViewPath().$tmpPath . $name . ".twig", $viewTemplate);
            $this->line("New view created successfully : <info>OK !</info>");
         }
		}
		


	}
}
