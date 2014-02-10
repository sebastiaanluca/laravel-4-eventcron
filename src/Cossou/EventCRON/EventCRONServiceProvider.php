<?php namespace Cossou\EventCRON;

use Cossou\EventCRON\Commands\Trigger;
use Cossou\EventCRON\Commands\TriggerAll;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class EventCRONServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = TRUE;

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

		$this->commands('eventcron.commands.trigger');
		$this->commands('eventcron.commands.trigger.all');
	}

	public function boot() {
		// Set package
		$this->package('cossou/eventcron');

		// Let users easily reference this class
		AliasLoader::getInstance()->alias('EventCRON', 'Cossou\EventCRON\Models\EventCRON');
	}
}