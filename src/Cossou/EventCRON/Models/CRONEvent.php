<?php namespace Cossou\EventCRON\Models;

use Carbon\Carbon;
use Eloquent;

class CRONEvent extends Eloquent {
	protected $table = 'eventcron';
	protected $guarded = array('id');
	protected $dates = array('execute_at', 'started_at', 'ended_at');

	/* */
	const UNPROCESSED_STATUS = 0;
	const PROCESSING_STATUS = 4;
	const PROCESSED_STATUS = 8;

	////////////////////////////////////////////////////////////////

	/**
	 * Returns the difference between start and end execution time in seconds.
	 *
	 * @return Carbon Seconds.
	 */
	public function getExecutionTime() {
		return $this->ended_at->diffInSeconds($this->started_at);
	}
}