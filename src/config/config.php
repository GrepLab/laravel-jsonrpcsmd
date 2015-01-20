<?php
return array(
    /**
     * Route to build and return the service map.
     * @type string
     */
    'route_prefix' => 'api/jsonrpcsmd',

    /**
     * The route used for attend the jsonrpc requests.
     * @type string
     */
    'route_api' => 'api/jsonrpc',

    /**
     * Extensions of files who can store services clases.
     * By default, only php files.
     * @type string[]
     */
    'allowed_extensions' => array('php'),

    /**
     * Generate a different url for each method using the service and method names.
     * @type boolean
     */
    'use_canonical' => false,

    /**
     * Throw an error if one class is not found.
     * @type boolean
     */
    'throw_if_path_not_exist' => false,

    /**
     * The route have to be install when the library is loaded.
     * @type boolean
     */
    'install_route_on_boot' => true,

    /**
     * Validator of services.
     * This closure is used to validate each class founded in the services path. This way you can limit the classes for
     * be indexed.
     * By default only the classes who its name end with the word "Service" will be indexed.
     * The first argument of the closure is the name of the class follow by the path of the file.
     * The return has to be TRUE for the class be indexed and FALSE if the class has to be ignored.
     * @type closure
     */
    'service_validator' => function(\ReflectionClass $reflectedclass) {
        $classname = $reflectedclass->getName();
        return ends_with($classname, 'Service');
    },

    /**
     * Closure to customize the visible name of the service.
     * This closure is useful if you want make changes to the name of the class visible in the service map.
     * The closure only have one argument. An instance of Greplab\Jsonrpcsmd\Smd\Service class.
     * This closure has to return the name of the service (including its namespace).
     * @type closure
     */
    'name_resolver' => null
);