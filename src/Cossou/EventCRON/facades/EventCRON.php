<?php namespace Cossou\EventCRON\Facades;

use Illuminate\Support\Facades\Facade;

class EventCRON extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'eventcron';
	}

}