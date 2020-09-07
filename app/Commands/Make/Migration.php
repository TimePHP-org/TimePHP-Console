<?php

namespace App\Commands\Make;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class Migration extends Command {
   /**
    * The signature of the command.
    *
    * @var string
    */
   protected $signature = 'make:migration {--table= : Name of the table you want to create}';

   /**
    * The description of the command.
    *
    * @var string
    */
   protected $description = 'Create the migration file';

   /**
    * Execute the console command.
    *
    * @return mixed
    */
   public function handle() {
      $table = $this->option('table');
      while ($table === null || empty($table)) {
         $table = $this->ask("Enter the name of the table you want to create");
      }

      $argName = strtolower($table);
      $tmpName = str_replace("migration", "", $argName);

      if (empty($tmpName)) {
         $this->line("Invalid controller name : <error>FAILED !</error>");
      } else {
         $migrationName = ucfirst($tmpName) . "Migration";
         $tableName = ucfirst($tmpName);

         if (file_exists($this->getMigrationPath() . DIRECTORY_SEPARATOR . $migrationName . ".php")) {
            $this->line("This migration already exists : <error>FAILED !</error>");
         } else {
            $migrationContent = str_replace("%Migration%", $migrationName, File::get($this->getTemplatePath() . DIRECTORY_SEPARATOR . "Migration.template"));
            $migrationContent = str_replace("%TableName%", $tableName, $migrationContent);

            File::put($this->getMigrationPath() . DIRECTORY_SEPARATOR . $migrationName . ".php", $migrationContent);
            $this->line("Migration created successfully : <info>OK !</info>");
         }
      }
   }

   /**
    * Get controllers path
    *
    * @return string
    */
   private function getMigrationPath(): string {
      return $this->getRootDirectory() . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "Migration";
   }

   /**
    * Get templates path
    *
    * @return string
    */
   private function getTemplatePath(): string {
      return $this->getRootDirectory() . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "templates";
   }


   /**
    * Return the root directory
    *
    * @return string
    */
   private function getRootDirectory(): string {
      return str_replace("/bin/bios/app/Commands/Make", "", str_replace("phar://", "", __DIR__));
   }
}
