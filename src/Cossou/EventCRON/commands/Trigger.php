<?php namespace Cossou\EventCRON\Commands;

use Cossou\EventCRON\Models\EventCron;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Trigger extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'eventcron:trigger';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Trigger a queued event. Use the --all switch to trigger all non-processed events.';

	////////////////////////////////////////////////////////////////

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments() {
		return [
			['event', InputArgument::REQUIRED, 'The events to trigger', NULL]
		];
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire() {
		$this->comment('Triggering non-processed queue items for "' . $this->argument('event') . '".');
		$count = EventCron::flushDB($this->argument('event'));

		if(is_null($count)) $this->error('EventCRON not enabled. See your configuration file.');
		else $this->info("Done! Triggered $count events.");
	}

}