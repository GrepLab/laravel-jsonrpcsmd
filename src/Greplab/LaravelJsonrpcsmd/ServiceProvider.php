<?php namespace Greplab\LaravelJsonrpcsmd;

/**
 * This class is the service provider for Laravel to easily use the greplab/jsonrpcsmd library.
 *
 * @author Daniel Zegarra <dzegarra@greplab.com>
 * @package Greplab\LaravelJsonrpcsmd
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * List of paths where the services files reside.
	 *
	 * @var array
	 */
	protected $paths = array();

	/**
	 * Bootstrap the application events.
	 *
	 * This method also register the route if the "install_route_on_boot" parameter is set on TRUE.
	 * If you doen't want to install the route during boot you have to change de value of the parameter
	 * "install_route_on_boot" to false before boot this service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('greplab/jsonrpcsmd');

		//Install the route on boot
		if (\Config::get('jsonrpcsmd::install_route_on_boot')) {
			$this->installDefaultRoute();
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//Every time you try to instance the class "JsonRpcSmdPort" you get $this
		\App::instance('JsonRpcSmdPort', $this);
	}

	/**
	 * Register a new path service to be indexed.
	 *
	 * @param string $path
	 * @param string $ns Namespace of the library path
	 */
	public function addServicePath($path, $ns=null)
	{
		$this->paths[] = array($path, $ns);
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
	 *
	 * @param string $route_prefix
	 */
	public function installRoute($route_prefix)
	{
	    \Route::get($route_prefix, function()
        {
            return $this->build();
        });
	}

	/**
	 * Build the service map.
	 *
	 * @return array
	 */
	public function build()
	{
		$mapper = \App::make('Greplab\LaravelJsonrpcsmd\Mapper');
		foreach ($this->paths as $path) {
			$mapper->addServicePath($path[0], $path[1]);
		}
		$mapper->build();
		return $this->outputResult($mapper);
	}

	/**
	 * Dump to the buffer the data pass as an argument.
	 *
	 * @param $result
	 * @return \Illuminate\Http\Response
	 */
	protected function outputResult($result)
	{
		if (is_object($result) && $result instanceof \Greplab\LaravelJsonrpcsmd\Mapper) {
			$result = $result->toJson();
		}else if (is_array($result) || is_object($result)) {
			$result = json_encode($result);
		}

		$r = \Response::make($result, 200);
		$r->header('Content-Type', 'application/json; charset=UTF-8');
		return $r;
	}
}
