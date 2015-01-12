<?php namespace Greplab\LaravelJsonrpcsmd;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		//Instalar automáticamente el route
		if (\Config::get('jsonrpcsmd::installRouteOnBoot')) {
			$this->installDefaultRoute();
		}
	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register() {
		$this->package('greplab/jsonrpcsmd');

		//Alias de esta instancia
		\App::instance('JsonRpcSmd', $this);

		//Instancio el mapeador de servicios
		\App::singleton('Greplab\LaravelJsonrpcsmd\Mapper', function() {
			return new \Greplab\LaravelJsonrpcsmd\Mapper;
		});
	}

	/**
	 * Register the default route.
	 */
	public function installDefaultRoute()
	{
		$this->installRoute(\Config::get('jsonrpcsmd::route_prefix'));
	}

	/**
	 * Register a customized route.
	 * @param string $route_prefix
	 */
	public function installRoute($route_prefix)
	{
	    \Route::get($route_prefix, function()
        {
            return \App::make('Greplab\LaravelJsonrpcsmd\Mapper')->build();
        });
	}

}
