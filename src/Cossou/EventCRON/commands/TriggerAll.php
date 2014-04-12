<?php namespace Cossou\EventCRON\Commands;

use App;
use Illuminate\Console\Command;

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
	protected $description = 'Trigger all queued events.';

	////////////////////////////////////////////////////////////////

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire() {
		$this->comment('Triggering all non-processed queue items.');
		$count = App::make('eventcron')->flushAll();

		if(is_null($count)) $this->error('EventCRON not enabled. See your configuration file.');
		else $this->info("Done! Triggered $count events.");
	}

}