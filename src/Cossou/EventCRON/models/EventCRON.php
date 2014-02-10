<?php namespace Cossou\EventCRON\Models;

use App;
use Carbon\Carbon;
use Config;
use Event;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Log;

class EventCron extends \Event {

	/**
	 * Register a callback for a given event.
	 *
	 * @param  string $event
	 * @param  array $data
	 * @param  int $date
	 * @return int
	 */
	public static function queue($event, $data = array(), Carbon $executeAt = NULL) {
		if(is_null($executeAt)) $executeAt = Carbon::now();
		return EventCronBase::create(array('event' => $event, 'arguments' => serialize($data), 'execute_at' => $executeAt));
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
			$queues = EventCronBase::where('status', '=', EventCRONBase::UNPROCESSED_STATUS)
				->where('execute_at', '<=', Carbon::now())
				->orderBy('created_at', 'ASC')
				->take(Config::get('eventcron::config.max_events_per_execution'))
				->get();

			$totalExecutionTime = 0;

			foreach($queues as $queue) {
				$queue->started_at = Carbon::now();
				$queue->status = EventCRONBase::PROCESSING_STATUS;
				$queue->save(); // Extra query though

				// Fire away!
				Event::fire($queue->queue, unserialize($queue->arguments));

				$queue->status = EventCRONBase::PROCESSED_STATUS;
				$queue->ended_at = Carbon::now();
				$queue->save(); // Save again

				if(Config::get('eventcron::config.log_events')) {
					Log::info('Event [' . $queue->event . '-' . $queue->id . '] was executed! Execution time (in seconds): ' . $queue->getExecutionTime());
				}

				$totalExecutionTime += $queue->getExecutionTime();
			}

			if(Config::get('eventcron::config.log_events')) {
				Log::info('Cronjob ended! Total execution time (in seconds): ' . $totalExecutionTime);
			}

			return $queues->count();
		} else {
			throw new Exception("This function can only be executed by the CLI (php artisan Eventcron::run queue-name) or just change the configuration file.", 1);
		}
	}

	// TODO: clear old entries
}
