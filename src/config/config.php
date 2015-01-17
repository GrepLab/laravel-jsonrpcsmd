<?php
return array(
    /**
     * Route que va a disparar la generación del mapa de servicios.
     * @type string
     */
    'route_prefix' => 'api/jsonrpcsmd',
    /**
     * Route utilizado para ejecutar las llamadas remotas.
     * Este será indicado en el mapa generado.
     * @type string
     */
    'route_api' => 'api/jsonrpc',
    /**
     * Extensiones de archivos que pueden albergar servicios.
     * @type string[]
     */
    'allowed_extensions' => array('php'),
    /**
     * Generar un URL distinto para cada método y servicio.
     * El nombre del servicio y el método estará incluido en el URL.
     * @type boolean
     */
    'use_canonical' => false,
    /**
     * Throw an error if une class is not found.
     * @type boolean
     */
    'throwIfPathNotExist' => false,
    /**
     * The route have to be install when the library is loaded.
     * @type boolean
     */
    'installRouteOnBoot' => true,
    /**
     * Validator of services.
     * This closure is used to validate each class founded in the services path. This way you can limit the classes to
     * index.
     * @type closure
     */
    'serviceValidator' => function($classname, $file) {
        return ends_with($classname, 'Service');
    }
);