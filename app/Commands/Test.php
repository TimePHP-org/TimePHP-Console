<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Test extends Command
{
	/**
   * The signature of the command.
   *
   * @var string
   */
 	protected $signature = 'test';

	/**
   * The description of the command.
   *
   * @var string
   */
	protected $description = 'Test comand';

	/**
   * Execute the console command.
   *
   * @return mixed
   */
	public function handle()
	{
      $primary = false;
      $fields = ["primary", "type", "nullable", "unique", "default"];
      $data = [];
      while(true){
         $newField = $this->ask("Add a field (default: yes)") ?? "yes";
         if($newField === "yes"){
            $this->info($newField);
         } else {
            break;
         }
      }

   }
}
