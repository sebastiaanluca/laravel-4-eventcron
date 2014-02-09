<?php namespace Cossou\EventCRON\Models;

class EventCRONBase extends \Eloquent {
	protected $table = 'eventcron';
	protected $guarded = array('id');
}