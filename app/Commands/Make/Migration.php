<?php

namespace App\Commands\Make;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use App\Traits\Foundation;

class Migration extends Command {

   use Foundation;
   
   /**
    * The signature of the command.
    *
    * @var string
    */
   protected $signature = 'make:migration {--name= : Name of the migration you want to create}';

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
      $table = $this->option('name');
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
}
