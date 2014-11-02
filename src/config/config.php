<?php
return array(
    /**
     * Ruta del servicio smd.
     * @type string
     */
    'route_prefix' => 'api/jsonrpc',
    /**
     * Ruta para ejecutar llamadas remotas.
     * @type string
     */
    'route_api' => 'api/jsonrpc',
    /**
     * Extensiones de archivos que pueden albergar servicios.
     * @type string[]
     */
    'allowed_extensions' => array('php'),
    /**
     * Mostrar el nombre del servicio y el método en el URL.
     * @type boolean
     */
    'use_canonical' => false,
    /**
     * Directorios de servicios por defecto.
     * @type string[]
     */
    'service_paths' => array('/services'),
    /**
     * Lanzar un error si un directorio de servicios no es encontrado.
     * @type boolean
     */
    'throwIfPathNotExist' => false,
    /**
     * Indica si se debe instalar el route inmediatamente cuando se cargue el service provider.
     * @type boolean
     */
    'installRouteOnBoot' => true
);