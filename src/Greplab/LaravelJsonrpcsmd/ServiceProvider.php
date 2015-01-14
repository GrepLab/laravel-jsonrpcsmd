<?php namespace Greplab\LaravelJsonrpcsmd;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * List of paths where the services reside.
	 * @var array
	 */
	protected $paths = array();

	/**
	 * Bootstrap the application events.
	 * @return void
	 */
	public function boot()
	{
		$this->package('greplab/jsonrpcsmd');

		//Install the route on boot
		if (\Config::get('jsonrpcsmd::installRouteOnBoot')) {
			$this->installDefaultRoute();
		}
	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register() {

		\App::instance('JsonRpcSmd', $this);
	}

	/**
	 * Register a new path service to index.
	 * @param string $path
	 */
	public function addServicesPath($path)
	{
		$this->paths[] = $path;
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
            $this->build();
        });
	}

	/**
	 * @return mixed
	 */
	public function build()
	{
		$mapper = \App::make('Greplab\LaravelJsonrpcsmd\Mapper');
		foreach ($this->paths as $path) {
			$mapper->addServicePath($path);
		}
		return $mapper->build();
	}
}
