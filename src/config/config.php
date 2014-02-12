<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Enabled
	|--------------------------------------------------------------------------
	| Is it working or is it not working?
	|
	*/

	'enabled'                  => TRUE,

	/*
	|--------------------------------------------------------------------------
	| Run only from CLI
	|--------------------------------------------------------------------------
	| Only from command line (php artisan blablabla) or false to execute from
	| any place you want. Be sure that other people can't access the route
	|
	*/

	'run_only_from_cli'        => TRUE,

	/*
	|--------------------------------------------------------------------------
	| Max events per execution
	|--------------------------------------------------------------------------
	| Limit the number of rows to execute everytime the CronJob is called.
	|
	*/

	'max_events_per_execution' => '50',

	/*
	|--------------------------------------------------------------------------
	| Log events
	|--------------------------------------------------------------------------
	| Log every execution to the log. (If you run from CLI, make sure you
	| have permissions to write to the log).
	|
	*/

	'log_events'               => FALSE,
);