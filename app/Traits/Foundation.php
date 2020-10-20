<?php

namespace App\Traits;

trait Foundation {
   /**
    * Get migration path
    *
    * @return string
    */
   private function getMigrationPath(): string {
      return $this->getRootDirectory() . DIRECTORY_SEPARATOR . "App" . DIRECTORY_SEPARATOR . "Migration";
   }

   /**
    * Get Repository path
    *
    * @return string
    */
   private function getRepositoryPath(): string {
      return $this->getRootDirectory() . DIRECTORY_SEPARATOR . "App" . DIRECTORY_SEPARATOR . "Bundle" . DIRECTORY_SEPARATOR . "Repository";
   }

   /**
    * Get controllers path
    *
    * @return string
    */
   private function getControllerPath(): string {
      return $this->getRootDirectory() . DIRECTORY_SEPARATOR . "App" . DIRECTORY_SEPARATOR . "Bundle" . DIRECTORY_SEPARATOR . "Controllers";
   }

   /**
    * Get Entity path
    *
    * @return string
    */
   private function getEntityPath(): string {
      return $this->getRootDirectory() . DIRECTORY_SEPARATOR . "App" . DIRECTORY_SEPARATOR . "Bundle" . DIRECTORY_SEPARATOR . "Entity";
   }

   /**
    * Get views path
    *
    * @return string
    */
   private function getViewPath(): string {
      return $this->getRootDirectory() . DIRECTORY_SEPARATOR . "App" . DIRECTORY_SEPARATOR . "Bundle" . DIRECTORY_SEPARATOR . "Views" . DIRECTORY_SEPARATOR;
   }

   /**
    * Return the root directory
    *
    * @return string
    */
   private function getRootDirectory(): string {
      return str_replace("/bin/cli/app/Traits", "", str_replace("phar://", "", __DIR__));
   }

   /**
    * Controller template
    *
    * @return string
    */
   private function getControllerTemplate(): string{
      return "<?php

/**
 * PHP version 7.4.2
 * 
 * @author Robin Bidanchon <robin.bidanchon@gmail.com>
 */
      
namespace App\Bundle\Controllers;
      
use TimePHP\Foundation\AbstractController;
      
/**
 * @category Controller
 * @package TimePHP
 * @subpackage Bundle\Controllers
 */
class %Controller% extends AbstractController
{
      
}";

   }

   /**
    * Entity template
    *
    * @return string
    */
   private function getEntityTemplate(): string{

      return '<?php

namespace App\Bundle\Entity;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class %Entity% extends Model {


   /**
    * The table associated with the model.
    *
    * @var string
    */
   protected $table = "%Entity%";
   
   /**
    * The primary key associated with the table.
    *
    * @var string
    */
   protected $primaryKey = "uuid";

   /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
   public $incrementing = false;

   /**
    * The "type" of the auto-incrementing ID.
    *
    * @var string
    */
   protected $keyType = "string";

   /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
   public $timestamps = %timestamped%;

   %ConstTimestamps%

   /**
    * Indicates fillable properties
    *
    * @var array
    */
   protected $fillable = [\'uuid\', %fields%];

   public static function boot(){
      parent::boot();
      static::creating(function ($model) {
         if (! $model->getKey()) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
         }
      });
   }

   // Add the relationship here

}';
   }


   /**
    * Entity template
    *
    * @return string
    */
    private function getUserTemplate(): string{

      return '<?php

namespace App\Bundle\Entity;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class %Entity% extends Model {


   /**
    * The table associated with the model.
    *
    * @var string
    */
   protected $table = "%Entity%";
   
   /**
    * The primary key associated with the table.
    *
    * @var string
    */
   protected $primaryKey = "uuid";

   /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
   public $incrementing = false;

   /**
    * The "type" of the auto-incrementing ID.
    *
    * @var string
    */
   protected $keyType = "string";

   /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
   public $timestamps = %timestamped%;

   %ConstTimestamps%

   /**
    * Indicates fillable properties
    *
    * @var array
    */
   protected $fillable = [\'uuid\', %fields%];

   /**
    * Indicates hidden properties
    *
    * @var array
    */
   protected $hidden = [\'password\', \'role\'];


   public static function boot(){
      parent::boot();
      static::creating(function ($model) {
         if (! $model->getKey()) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
         }
      });
   }

   // Add the relationship here
   
}';
   }


   private function getMigrationTemplate(): string {
      return '<?php

namespace App\Migration;

use TimePHP\Database\AbstractMigration;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class %Migration% extends AbstractMigration {

   /**
    * Create the correspondong table in the database
    *
    * @return void
    */
   public function up(): void {
      if (!Capsule::schema()->hasTable("%TableName%")) {
         Capsule::schema()->create("%TableName%", function (Blueprint $table) {
            $table->uuid("uuid")->unique();

            %fields%
            %timestamps%

            $table->primary("uuid");
         });
   
      }
   }

   /**
    * Drop the corresponding table
    *
    * @return void
    */
   public function down(): void {
      Capsule::schema()->dropIfExists("%TableName%");
   }
}';

   }

   private function getRepositoryTemplate(){
      return '<?php

namespace App\Bundle\Repository;

use App\Bundle\Entity\%entity%;
use Illuminate\Database\Capsule\Manager;

class %repository% {


}';
   }

   private function getViewTemplate() {
      return '{% extends "%layoutPath%template/layout.twig" %}
{% block body %}

   

{% endblock %}';
   }



}