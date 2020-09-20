<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use App\Traits\Foundation;

class Test extends Command
{

   use Foundation;

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
      $this->info($this->getRootDirectory());
   }
}
