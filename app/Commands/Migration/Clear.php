<?php

namespace App\Commands\Migration;

use App\Traits\Foundation;
use LaravelZero\Framework\Commands\Command;

class Clear extends Command {

	use Foundation;
	
	/**
	 * 
	 */
	const MAX_SIMILAR = 3;

   /**
    * The signature of the command.
    *
    * @var string
    */
   protected $signature = 'migration:clear {--name= : Type of migration you want to clear}';

   /**
    * The description of the command.
    *
    * @var string
    */
   protected $description = 'Clear one or more migrations';

   /**
    * Execute the console command.
    *
    * @return mixed
    */
   public function handle() {

      
      
      $migrations = [];
      
      foreach (glob("{$this->getMigrationPath()}/*.php") as $file) {
         $tmpName = explode("/", $file);
         $tmpFile = end($tmpName);
         $migrations["files"][] = $tmpFile;
         $migrations["names"][] = str_replace("migration", "", strtolower(explode(".", $tmpFile)[0]));
      }


      if(count($migrations) > 0) {
         

         $name = $this->option("name");
         while ($name === null || empty($name)) {
            $name = $this->ask("Which migration do you want to clear ? (to clear all migrations, type all)");
         }
         
         $name = strtolower($name);
         $name = str_replace("migration", "", $name);

         if (empty($name) || $name === null) {
            $this->line("<error>Error !</error> Invalid name, try again");
            return;
         }
      

         if ($name === "all") {
            foreach (glob("{$this->getMigrationPath()}/*Migration.php") as $file) {
               unlink($file);
            }
            $this->line("Successfully deleted all migration files <info>OK !</info>");
            return;
         } else {

            $migrationDelete = $this->closestWord($migrations["names"], $name);

            if($migrationDelete["shortest"] > self::MAX_SIMILAR) {
               $this->line("<error>Error !</error> Can't find the corresponding migration file for : $name");
               return;
            } else if($migrationDelete["shortest"] <= self::MAX_SIMILAR && $migrationDelete["shortest"] > 0) {
               $closeMatch = $this->ask("Did you mean : ".ucfirst($migrationDelete["closest"])." ? (y/n)");
               while(!in_array($closeMatch, ["y", "n"])) {
                  $closeMatch = $this->ask("Did you mean : ".ucfirst($migrationDelete["closest"])." ? (y/n)");
               }

               $closeMatch = $closeMatch === "y" ? true : false;

               if($closeMatch){
                  $migrationName = ucfirst($migrationDelete["closest"])."Migration.php";
                  unlink($this->getMigrationPath() . "/$migrationName");
                  $this->line("Successfully deleted $migrationName file <info>OK !</info>");
                  return;
               } else {
                  return;
               }

            } else {
               $migrationName = ucfirst($migrationDelete["closest"])."Migration.php";
               unlink($this->getMigrationPath() . "/$migrationName");
               $this->line("Successfully deleted $migrationName file <info>OK !</info>");
               return;
            }
         }
      } else {
         $this->line("<comment>Warning !</comment> Migration folder is empty");
      }
   }

   private function closestWord(array $words, string $input) {
      $shortest = -1;
      foreach ($words as $word) {
         $lev = levenshtein($input, $word);
         if ($lev == 0) {
            $closest = $word;
            $shortest = 0;
            break;
         }
         if ($lev <= $shortest || $shortest < 0) {
            $closest = $word;
            $shortest = $lev;
         }
		}
		return [
			"closest" => $closest,
			"shortest" => $shortest
		];
   }
}
