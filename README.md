# Laravel EventCron Bundle
=======================

Laravel bundle to queue Events to a database and later fire them through artisan or a CronJob.

## Introduction

I created this bundle to run events in a queue every x minutes through a CronJob (or manually).

It's an extension of the original [_Laravel Event_](http://laravel.com/docs/events "Event"), where you can queue events to table to be fired later.

### Some examples

__*Example 1:*__ 

You want to schedule an e-mail to an user 24h after the registration.  
```php
	EventCron::queueDB('email24', array('user_id' => 1, 'message' => 'welcome'), date("Y-m-d H:i:s", strtotime("+24 hours")));
```

__*Example 2:*__ 

You have a time consumption action and you don't want to block the execution ([Kind of non-blocking execution](http://en.wikipedia.org/wiki/Non-blocking_algorithm) :).

__*Example 3:*__ 

You can also think about newsletter systems.

__Note:__ this bundle does not attempt to be a replacement for Workers. You are not off loading processes to another machine. The processes are still executed in the same server has your own app.

## Installation

    php artisan bundle:install eventcron

Add it to __application/bundles.php__:
```php
    return array(
        ...
        'eventcron' => array(
            'auto'  => true
        ),
        ...
    );
```   
Now __migrate__ to create the table:

	php artisan migrate eventcron

Now play with it. For example:
```php	
	Route::get('addevent', function()
	{
		...
		// In Real time: 
		// Event::fire('log_something', array('FOOBAR', array('foo' => 'bar')));

		// With EventCron:
		EventCron::queueDB('log_something', array('FOOBAR', array('foo' => 'bar')));
		...
	});
	
	// The event
	Event::listen('log_something', function($str, $arr)
	{
		// Note: If you use a bundle inside please use Bundle::start('yourbundle');
		Log::info('str => ' . $str . ' arr => ' . print_r($arr, true));
	});
```	
Setup the CronJob:

	*/1 * * * * root php /var/www/laravel/artisan Eventcron::run log_something --env=local
	
Or just run the command:

	php artisan Eventcron::run log_something --env=local

__Note:__ If your CLI PHP doesn't have permissions to write to the log file just add "sudo php artisan ..." 
	
Thats it, the cron will fire all the __log_something__ events in the queue by [FIFO](http://en.wikipedia.org/wiki/FIFO) order.

If you want there is a method to run all the queues in the table:

	php artisan Eventcron::run:runall --env=local

You can also fire the events through a route (*which I don't recommend*). Just change the eventcron/config/config.php file to allow it:

	'run_only_from_cli' => false,
	
And then:
```php
	Route::get('fire', function()
	{
		EventCron::flushDB('log_something');
	});
```	
Notice you can pass a __date__ to the queue:
```php
	 EventCron::queueDB('log_something', array('FOOBAR', array('foo' => 'bar')), date("Y-m-d H:i:s", strtotime("+2 hours")));
```
## API	 

Add event to a queue:
```php
	EventCron::queueDB($name = "string", $args = array() [, $date = date("Y-m-d H:i:s")]);
```
Flush events from a queue:
```php
	EventCron::flushDB($name = "string");
```
Flush all the events:
```php
	EventCron::flushAllDB();
```

## Configurations

__config.php__ (eventcron/config/config.php)

###enabled (default true)

Well, this one is easy. Right?

	BOOLEAN true / false
	
###run_only_from_cli (default true)

Allow the CronJob to be run only from CLI (artisan).

	BOOLEAN true / false
	
###max_events_per_execution (default 50)

Max number of events to fire in one run (set it to a lower number if you don't want the server go slower).

	INTEGER number

###log_events (default true)

	BOOLEAN true / false
	
##Questions? Problems?

Drop me a line at <cossou@gmail.com> or [@cossou](https://twitter.com/cossou)