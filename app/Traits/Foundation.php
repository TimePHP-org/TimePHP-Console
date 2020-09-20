<?php

namespace App\Traits;

trait Foundation {
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
    * Return the root directory
    *
    * @return string
    */
   private function getRootDirectory(): string {
      return str_replace("/bin/bios/app/Commands/Make", "", str_replace("phar://", "", __DIR__));
   }
}