<?php

/**
 * Registering an autoloader
 */
$loader = new \Phalcon\Loader();

$loader->registerNamespaces(
    [
        'Controller' => $config->application->controllersDir,
        'Model'      => $config->application->modelsDir,
        'Library'    => $config->application->libraryDir,
        'Middleware' => $config->application->middlewareDir,
    ]
)->register();
