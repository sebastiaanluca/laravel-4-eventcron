<?php namespace Cossou\EventCRON;

use Cossou\EventCRON\Commands\Trigger;
use Cossou\EventCRON\Commands\TriggerAll;
use Illuminate\Support\ServiceProvider;

class EventCRONServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 * Set to false (default) to load configuration at boot
	 *
	 * @var bool
	 */
	protected $defer = FALSE;

	////////////////////////////////////////////////////////////////

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
		/* Register commands */
		$this->app['eventcron.commands.trigger'] = $this->app->share(function ($app) {
			return new Trigger();
		});

		$this->app['eventcron.commands.trigger.all'] = $this->app->share(function ($app) {
			return new TriggerAll();
		});

		$this->commands(['eventcron.commands.trigger', 'eventcron.commands.trigger.all']);

		/* Register singleton in IoC container */
		$this->app['eventcron'] = $this->app->share(function ($app) {
			return $app->make('Cossou\EventCRON\EventCRONManager');
		});
	}

	public function boot() {
		// Set package
		$this->package('cossou/eventcron');
	}
}