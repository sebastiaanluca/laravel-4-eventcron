<?php namespace Cossou\EventCRON\Models;

use App;
use Carbon\Carbon;
use Config;
use Event;
use Exception;
use Log;

class EventCron extends \Event {

	// TODO: clear old entries (config option to check when flushing queues + manual function call)(params? Relative date, number of processed entries, ... Always for processed/cancelled entries)
	// TODO: cancel all unprocessed events of a specific type (new status)
	// TODO: cancel all unprocessed events (also new status)
	// TODO: get a list of all events of a specific type with a specific status (either by array or something like `UNPROCESSED | PROCESSED | PROCESSING`)
	// TODO: get a list of all events with a specific status (either by array or something like `UNPROCESSED | PROCESSED | PROCESSING`)

	/**
	 * Execute an array of events stored as queues in the database.
	 *
	 * @param $events
	 * @return int
	 */
	protected static function executeQueue($events) {
		$totalExecutionTime = 0;

		foreach($events as $queue) {
			$queue->started_at = Carbon::now();
			$queue->status = EventCRONBase::PROCESSING_STATUS;
			$queue->save(); // Extra query though

			// Fire away!
			Event::fire($queue->event, unserialize($queue->arguments));

			$queue->status = EventCRONBase::PROCESSED_STATUS;
			$queue->ended_at = Carbon::now();
			$queue->save(); // Save again

			if(Config::get('eventcron::config.log_events')) {
				Log::info('Event [' . $queue->event . '-' . $queue->id . '] was executed! Execution time (in seconds): ' . $queue->getExecutionTime());
			}

			$totalExecutionTime += $queue->getExecutionTime();
		}

		return $totalExecutionTime;
	}

	////////////////////////////////////////////////////////////////

	/**
	 * Queue an event to trigger it later.
	 *
	 * @param  string $event
	 * @param  array  $data    Optional arguments
	 * @param  Carbon $execute At Date to execute the event at
	 * @return int
	 */
	public static function queue($event, $data = array(), Carbon $executeAt = NULL) {
		if(is_null($executeAt)) $executeAt = Carbon::now();
		return EventCronBase::create(array('event' => $event, 'arguments' => serialize($data), 'execute_at' => $executeAt));
	}

	/**
	 * Trigger all unprocessed event queues of a specific type.
	 *
	 * @param  string $queue
	 * @return null
	 * @throws \Exception
	 */
	public static function flush($queue) {
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

			$events = EventCronBase::where('event', '=', $queue)
				->where('status', '=', EventCRONBase::UNPROCESSED_STATUS)
				->where('execute_at', '<=', Carbon::now())
				->orderBy('created_at', 'ASC')
				->take(Config::get('eventcron::config.max_events_per_execution'))
				->get();

			$totalExecutionTime = static::executeQueue($events);

			if(Config::get('eventcron::config.log_events')) {
				Log::info('Cronjob ended! Total execution time (in seconds): ' . $totalExecutionTime);
			}

			return $events->count();
		} else {
			throw new Exception("This function can only be executed by the CLI (php artisan Eventcron::run queue-name) or just change the configuration file.", 1);
		}
	}

	/**
	 * Trigger all unprocessed event queues.
	 *
	 * @return null
	 * @throws \Exception
	 */
	public static function flushAll() {
		if(!Config::get('eventcron::config.enabled'))
			return NULL;

		if(Config::get('eventcron::config.run_only_from_cli') ? App::runningInConsole() : TRUE) {
			if(Config::get('eventcron::config.log_events')) {
				Log::info('Cronjob started!');
			}

			$events = EventCronBase::where('status', '=', EventCRONBase::UNPROCESSED_STATUS)
				->where('execute_at', '<=', Carbon::now())
				->orderBy('created_at', 'ASC')
				->take(Config::get('eventcron::config.max_events_per_execution'))
				->get();

			$totalExecutionTime = $totalExecutionTime = static::executeQueue($events);

			if(Config::get('eventcron::config.log_events')) {
				Log::info('Cronjob ended! Total execution time (in seconds): ' . $totalExecutionTime);
			}

			return $events->count();
		} else {
			throw new Exception("This function can only be executed by the CLI (php artisan Eventcron::run queue-name) or just change the configuration file.", 1);
		}
	}
}
