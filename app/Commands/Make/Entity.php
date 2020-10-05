<?php

namespace App\Commands\Make;

use LaravelZero\Framework\Commands\Command;
use App\Traits\Foundation;
use Illuminate\Support\Facades\File;

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

        
        // creation de l'entity
        $entityTemplate = $this->getEntityTemplate();
        $entityTemplate = str_replace("%Entity%", $entityName, $entityTemplate);
        $entityTemplate = str_replace("%timestamped%", $fields["timestamped"] ? 'true' : 'false', $entityTemplate);

        if($fields["timestamped"]){
            $tmpTimestamped = "const CREATED_AT = 'createdAt';
   const UPDATED_AT = 'updatedAt';";
            $entityTemplate = str_replace("%ConstTimestamps%", $tmpTimestamped, $entityTemplate);
        }

        $tmpFields = array_column($fields, 'field');
        $tmpFields = array_map(function($element) {
            return "'$element'";
        }, $tmpFields);

        $entityTemplate = str_replace("%fields%", implode(", ", $tmpFields), $entityTemplate);

        File::put($this->getEntityPath() . DIRECTORY_SEPARATOR . $entityName . ".php", $entityTemplate);
        $this->line("Entity created successfully : <info>OK !</info>");

        // Repository

        $repositoryTemplate = $this->getRepositoryTemplate();
        $repositoryName = $entityName."Repository";
        $repositoryTemplate = str_replace("%entity%", $entityName, $repositoryTemplate);
        $repositoryTemplate = str_replace("%repository%", $repositoryName, $repositoryTemplate);
        File::put($this->getRepositoryPath() . DIRECTORY_SEPARATOR . $repositoryName . ".php", $repositoryTemplate);
        $this->line("Repository created successfully : <info>OK !</info>");



        // creation de la migration
        $migrationName = $entityName."Migration";
        $migrationTemplate = $this->getMigrationTemplate();

        $migrationTemplate = str_replace("%TableName%", $entityName, $migrationTemplate);
        $migrationTemplate = str_replace("%Migration%", $migrationName, $migrationTemplate);

        $tmpTimestamped = '$table->timestamp("createdAt")->default(Capsule::raw("CURRENT_TIMESTAMP"));
            $table->timestamp("updatedAt")->default(Capsule::raw("CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP"));';
        
        $migrationTemplate = str_replace("%timestamps%", $fields["timestamped"] ? $tmpTimestamped : "", $migrationTemplate);

        unset($fields["timestamped"]);

        $tmp = "";

        // "boolean", "char", "date", "dateTime", "float", "integer", "longText", "tinyInteger", "string", "text"
        foreach($fields as $field){
            $tmp .= '$table';
            $tmp .= sprintf("->%s", $field["type"]);
            if(in_array($field["type"], ["string", "char"]) && $field["length"] !== null){
                $tmp .= sprintf("('%s', %d)", $field["field"], $field["length"]);
            } else {
                $tmp .= sprintf("('%s')", $field["field"]);
            }

            if(in_array($field["type"], ["integer", "float"]) && $field["unsigned"] !== null){
                $tmp .= "->unsigned()";
            }

            if($field["nullable"]){
                $tmp .= "->nullable()";
            }

            if($field["default"] !== null && in_array($field["type"], ["string", "char"])){
                $tmp .= sprintf("->default('%s')", $field["default"]);
            } else if($field["default"] !== null && in_array($field["type"], ["integer", "float"])){
                $tmp .= sprintf("->default(%d)", $field["default"]);
            }

            $tmp .= ";
            ";
        }

        $migrationTemplate = str_replace("%fields%", $tmp, $migrationTemplate);

        File::put($this->getMigrationPath() . DIRECTORY_SEPARATOR . $migrationName . ".php", $migrationTemplate);
        $this->line("Migration created successfully : <info>OK !</info>\n");
        $this->line("Use <info>migration:up</info> to create the $entityName table in your database");
        $this->line("Use <info>migration:down</info> to delete the $entityName table in your database");


     }
   }

}
