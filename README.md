# Laravel 4 EventCRON

A Laravel 4 package that enables you to queue events and fire them in sequence, or at a specific time in the future.


## Use Cases

- Sending a user an e-mail 24 hours after registration
- Scheduled mailing and newsletters
- Time consuming processes that would run better at night
- Firing events in a continuous matter (just queue a new event once it's fired)


## Installation

Add the package to your composer.json and run `composer update`.

```
{
	"require": {
		"cossou/eventcron": "*"
	}
}
```

Add the service provider in `app/config/app.php`:

```php
'providers' => [
	…
	
	'Cossou\EventCRON\EventCRONServiceProvider'
]
```

__Optionally__ add the facade to your aliases:

```php
'aliases' => [
	…
	
	'EventCRON' => 'Cossou\EventCRON\Facades\EventCRON'
]
```

Perform the migration to create the database tables:

```
php artisan migrate --package=cossou/eventcron
```


## How to Use

To get started, there are three ways in which you can utilize this package.

Via the facade, if you added it to your configuration file (preferred way):

```php
EventCRON::queue('myevent');
```

By using the Laravel IoC container:

```php
App::make('eventcron')->queue('myevent');
```

Directly through the class:

```php
$eventcron = new Cossou\EventCRON\EventCRONManager();
$eventcron->queue('myevent');
```


### Adding Events

#### First Steps

As shown, you can just queue your event and listen to it elsewhere.

```php
EventCRON::queue('myevent');
```

```php
Event::listen('myevent', function()
{
	echo 'myevent just got fired!';
});
```

Flushing the queue for this event will fire all of them at once, since no time has been set.


#### Using arguments

You can also pass some data to your event handler in the form of an array.

```php
EventCRON::queue('myevent', ['string', $variable, 12, new Object()]);
```

Laravel will then extract all of these variables and pass them to your event handler:

```php
Event::listen('myevent', function($string, $variable, $number, $object)
{
	echo 'myevent just got fired with some neat arguments';
	dd($string, variable, $number, $object);
});
```


#### Timing Is Everything

Of course, the main idea of this package is to schedule your events. Just pass a Carbon instance as third parameter:

```php
EventCRON::queue('myevent', NULL, Carbon\Carbon::now()->addHour());
```

This event will only be triggered one hour from now.

_Carbon is a nice extension to PHP's datetime class(es). For more info: https://github.com/briannesbitt/Carbon._


### Flushing the Queue

Now that you've added all these events, you'd want them to be triggered so your whole setup actually does something.

To trigger the queue for a single event:

```php
EventCRON::flush('myevent');
```

To trigger the queue of all events:

```php
EventCRON::flushAll();
```

__Please note:__ events with an execution time set will only be triggered if that moment is in the past. In addition, if the configuration file states `enabled` as `false` or `run_only_from_cli` as `true` (and you're flushing a queue from code), nothing will happen.


### The CLI + Creating a CRON Job

The following commands are used to flush queues from the CLI:

```
php artisan eventcron:trigger myevent
```

```
php artisan eventcron:trigger:all
```

On most occasions though, you'd trigger events in your queue with a CRON job instead of directly from code or the CLI.

Use `crontab -e` or `sudo crontab -e` to get into your CRON file and add the following line at the end to flush all queues every minute (because you never know when you've scheduled an event):

```
*/1 * * * * php /var/www/myproject/artisan eventcron:trigger:all
```


## Configuration

Publish the package's configuration file with:

```
php artisan config:publish cossou/eventcron
```

### enabled (default `true`)

Simply enable or disable the package.

	BOOLEAN true / false
	
### run_only_from_cli (default `true`)

Allow the flushing of queues only from your command line interface (CLI).

	BOOLEAN true / false
	
### max_events_per_execution (default `50`)

Maximum number of events to fire in one run (set it to a lower number if you don't want the server go slower).

	INTEGER number

### log_events (default `false`)

Whether or not to write debug messages your log.

	BOOLEAN true / false