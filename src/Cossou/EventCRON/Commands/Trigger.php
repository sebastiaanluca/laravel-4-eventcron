<?php namespace Cossou\EventCRON\Commands;

use App;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

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
	protected $description = 'Trigger a queued event.';

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
		$count = App::make('eventcron')->flush($this->argument('event'));

		if(is_null($count)) $this->error('EventCRON not enabled. See your configuration file.');
		else $this->info("Done! Triggered $count events.");
	}

}