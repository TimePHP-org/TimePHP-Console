<?php

namespace App\Commands\Make;

use LaravelZero\Framework\Commands\Command;
use App\Traits\Foundation;

class Entity extends Command {

    use Foundation;

   /**
    * The signature of the command.
    *
    * @var string
    */
   protected $signature = 'make:entity {--name= : Name of the entity you want to create}';

   /**
    * The description of the command.
    *
    * @var string
    */
   protected $description = 'Create the entity file';

   /**
    * Execute the console command.
    *
    * @return mixed
    */
   public function handle() {

      $entity = $this->option('name');
      while ($entity === null || empty($entity)) {
         $entity = $this->ask("Enter the name of the entity you want to create");
      }

      $argName = strtolower($entity);
      $tmpName = str_replace("entity", "", $argName);

      if (empty($tmpName)) {
         $this->line("Invalid Entity name : <error>FAILED !</error>");
      } else {

        $entityName = ucfirst($tmpName);
        $fields = [];
        $continue = true;
        $fieldNames = [];
        $index = 0;
        $types = ["boolean", "char", "date", "dateTime", "float", "integer", "longText", "tinyInteger", "string", "text"];

        while($continue){
            $fieldName = strtolower($this->ask("Name of the field"));
            while(in_array($fieldName, $fieldNames) || empty($fieldName)){
                if(in_array($fieldName, $fieldNames)) $this->error("This field already exists");
                if(empty($fieldName)) $this->error("Empty field are invalid");

                $fieldName = strtolower($this->ask("Enter a valid field name"));
            }
            $fieldNames[] = $fieldName;
            $fields[$index]["field"] = $fieldName;

            $type = strtolower($this->ask("What's the type of the field"));
            while(!in_array($type, $types)){
                $this->info("The must be in : boolean, char, date, dateTime, float, integer, longText, tinyInteger, string, text");
                $type = strtolower($this->ask("Enter the type of the field"));
            }
            $fields[$index]["type"] = $type;
            
            $nullable = strtolower($this->ask("Can this field be nullable (y/n)"));
            while(!in_array($nullable, ["y", "n", "yes", "no"])){
                $this->info("Invalid value");
                $nullable = strtolower($this->ask("Can this field be nullable (y/n)"));
            }
            $fields[$index]["nullable"] = (in_array($nullable, ["y", "yes"]) ? true : false);

            // $default = $this->ask("Default field value");
            if($index === 3){
                break;
            }
            $index++;
        }

        dd($fields);


        // if (file_exists($this->getEntityPath() . DIRECTORY_SEPARATOR . $entityName . ".php")) {
        //    $this->line("This entity already exists : <error>FAILED !</error>");
        // } else {
        //    $entityContent = str_replace("%Entity%", $entityName, File::get($this->getTemplatePath() . DIRECTORY_SEPARATOR . "Entity.template"));
        //    $entityContent = str_replace("%TableName%", $tableName, $entityContent);

        //    File::put($this->getEntityPath() . DIRECTORY_SEPARATOR . $entityName . ".php", $entityContent);
        //    $this->line("Entity created successfully : <info>OK !</info>");
        // }
     }
   }

}
