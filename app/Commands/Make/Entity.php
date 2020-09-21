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
        $types = ["boolean", "char", "date", "dateTime", "float", "integer", "longText", "tinyInteger", "string", "text"];
        $index = 0;

        while($continue){

            //! field name
            $fieldName = strtolower($this->ask("Name of the field"));
            while(in_array($fieldName, $fieldNames) || empty($fieldName)){
                if(in_array($fieldName, $fieldNames)) $this->error("This field already exists");
                if(empty($fieldName)) $this->error("Empty field are invalid");
                $fieldName = strtolower($this->ask("Enter a valid field name"));
            }
            $fieldNames[] = $fieldName;
            $fields[$index]["field"] = $fieldName;

            //! field type
            $type = strtolower($this->ask("What's the type of the field"));
            while(!in_array($type, $types)){
                $this->line("<error>Invalid type</error>, must be in : boolean, char, date, dateTime, float, integer, longText, tinyInteger, string, text");
                $type = strtolower($this->ask("Enter the type of the field"));
            }
            $fields[$index]["type"] = $type;


            //! string or char length
            if(in_array($type, ["string", "char"])){
                $length = $this->ask("Enter the length for the field ($fieldName)");
                while(!is_numeric($length)){
                    $this->error("Invalid length");
                    $length = $this->ask("Enter a valid length for the field ($fieldName)");
                }
                $fields[$index]["length"] = intval($length);
            } else {
                $fields[$index]["length"] = null;
            }

            //! unsigned
            if(in_array($type, ["integer", "float"])){
                $unsigned = strtolower($this->ask("Can this field be unsigned (y/n)"));
                while(!in_array($unsigned, ["y", "n", "yes", "no"])){
                    $this->error("Invalid value");
                    $unsigned = strtolower($this->ask("Can this field be unsigned (y/n)"));
                }
                $fields[$index]["unsigned"] = (in_array($unsigned, ["y", "yes"]) ? true : false);
            } else {
                $fields[$index]["unsigned"] = null;
            }

            
            //! nullable
            $nullable = strtolower($this->ask("Can this field be nullable (y/n)"));
            while(!in_array($nullable, ["y", "n", "yes", "no"])){
                $this->error("Invalid value");
                $nullable = strtolower($this->ask("Can this field be nullable (y/n)"));
            }
            $fields[$index]["nullable"] = (in_array($nullable, ["y", "yes"]) ? true : false);

            //! default
            $default = $this->ask("Do you want a default value (press enter to pass)");
            $fields[$index]["default"] = ($default !== null) ? $default : null;

            //! leave the loop
            $keepAdding = strtolower($this->ask("Add a new field ? (y/n)"));
            while(!in_array($nullable, ["y", "n", "yes", "no"])){
                $this->error("Invalid value");
                $keepAdding = strtolower($this->ask("Do you want to add a new field ? (y/n)"));
            }
            if(in_array($keepAdding, ["n", "no"])){
                $continue = false;
            }

            $index++;
        }

        $timestamped = strtolower($this->ask("Do you want this table to be timestamped (y/n)"));
        while(!in_array($timestamped, ["y", "n", "yes", "no"])){
            $this->error("Invalid value");
            $timestamped = strtolower($this->ask("Do you want this table to be timestamped (y/n)"));
        }
        $fields["timestamped"] = (in_array($timestamped, ["y", "yes"]) ? true : false);

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
