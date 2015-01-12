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