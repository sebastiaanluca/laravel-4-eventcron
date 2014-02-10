<?php namespace Cossou\EventCRON\Commands;

use Cossou\EventCRON\Models\EventCron;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class TriggerAll extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'eventcron:trigger:all';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Trigger all non-processed events.';

	////////////////////////////////////////////////////////////////

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire() {
		$this->comment('Triggering all non-processed queue items.');
		$count = EventCron::flushAll();

		if(is_null($count)) $this->error('EventCRON not enabled. See your configuration file.');
		else $this->info("Done! Triggered $count events.");
	}

}