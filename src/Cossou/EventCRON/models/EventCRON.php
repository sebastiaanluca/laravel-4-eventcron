<?php namespace Cossou\EventCRON\Models;

use App;
use Config;
use Event;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Log;

class EventCron extends \Event {

	/**
	 * Register a callback for a given event.
	 *
	 *
	 * @param  string $queue
	 * @param  array $data
	 * @param  int $date
	 * @return int
	 */
	public static function queueDB($queue, $data = array(), $date = 0) {
		return EventCronBase::create(array('queue' => $queue, 'arguments' => serialize($data), 'date' => $date));
	}

	/**
	 * Flush a queue of events from the table.
	 *
	 *
	 * @param  string $queue
	 * @return NULL|Integer
	 */
	public static function flushDB($queue) {
		if(!Config::get('eventcron::config.enabled')) {
			return NULL;
		}

		if(empty($queue)) {
			throw new Exception("No queue given.", 1);
		}

		if(Config::get('eventcron::config.run_only_from_cli') ? App::runningInConsole() : TRUE) {
			if(Config::get('eventcron::config.log_events')) {
				Log::info('Cronjob started!');
			}

			$queues = EventCronBase::where('queue', '=', $queue)
				->where('processed', '=', FALSE)
				->where('date', '<=', date('Y-m-d H:i:s'))
				->orderBy('created_at', 'ASC')
				->take(Config::get('eventcron::config.max_events_per_execution'))
				->get();

			foreach($queues as $queue) {
				$queue->started_at = (microtime(TRUE) * 10000);
				$queue->runned_at = date('Y-m-d H:i:s');

				Event::fire($queue->queue, unserialize($queue->arguments));

				if(Config::get('eventcron::config.log_events')) {
					Log::info('Event [' . $queue->queue . '-' . $queue->id . '] was executed!');
				}

				$queue->processed = TRUE;
				$queue->ended_at = (microtime(TRUE) * 10000);
				$queue->save();
			}

			if(Config::get('eventcron::config.log_events')) {
				Log::info('Cronjob ended!');
			}

			return $queues->count();
		} else {
			throw new Exception("This function can only be executed by the CLI (php artisan Eventcron::run queue-name) or just change the configuration file.", 1);
		}
	}

	public static function flushAllDB() {
		if(!Config::get('eventcron::config.enabled'))
			return NULL;

		if(Config::get('eventcron::config.run_only_from_cli') ? App::runningInConsole() : TRUE) {
			if(Config::get('eventcron::config.log_events')) {
				Log::info('Cronjob started!');
			}

			/**
			 * @var $queues Collection
			 */
			$queues = EventCronBase::where('processed', '=', FALSE)
				->where('date', '<=', date('Y-m-d H:i:s'))
				->orderBy('created_at', 'ASC')
				->take(Config::get('eventcron::config.max_events_per_execution'))
				->get();

			foreach($queues as $queue) {
				$queue->started_at = (microtime(TRUE) * 10000);
				$queue->runned_at = date('Y-m-d H:i:s');

				Event::fire($queue->queue, unserialize($queue->arguments));

				if(Config::get('eventcron::config.log_events')) {
					Log::info('Event [' . $queue->queue . '-' . $queue->id . ']  was executed!');
				}

				$queue->processed = TRUE;
				$queue->ended_at = (microtime(TRUE) * 10000);
				$queue->save();
			}

			if(Config::get('eventcron::config.log_events')) {
				Log::info('Cronjob ended!');
			}

			return $queues->count();
		} else {
			throw new Exception("This function can only be executed by the CLI (php artisan Eventcron::run queue-name) or just change the configuration file.", 1);
		}
	}

	// TODO: clear old entries
}
