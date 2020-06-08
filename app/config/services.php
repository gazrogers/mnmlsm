<?php
declare(strict_types=1);

use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Mvc\View\Simple as View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Url as UrlResolver;

/**
 * Shared configuration service
 */
$di->setShared(
    'config', fn() => include APP_PATH . "/config/config.php"
);

/**
 * Sets the view component
 */
$di->setShared(
    'view',
    function () {
        $config = $this->getConfig();

        $view = new View();
        $view->setViewsDir($config->application->viewsDir);

        $view->registerEngines(
        [
            '.volt' => function ($view) {
                $config = $this->getConfig();

                $volt = new VoltEngine($view, $this);

                $volt->setOptions(
                    [
                        'path' => $config->application->cache->viewsDir,
                        'separator' => '_'
                    ]
                );

                return $volt;
            },
            '.phtml' => PhpEngine::class

        ]);
        return $view;
    }
);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared(
    'url',
    function () {
        $config = $this->getConfig();

        $url = new UrlResolver();
        $url->setBaseUri($config->application->baseUri);
        return $url;
    }
);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared(
    'db',
    function () {
        $config = $this->getConfig();

        $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
        $params = [
            'host'     => $config->database->host,
            'username' => $config->database->username,
            'password' => $config->database->password,
            'dbname'   => $config->database->dbname,
            'charset'  => $config->database->charset
        ];

        if ($config->database->adapter == 'Postgresql') {
            unset($params['charset']);
        }

        $connection = new $class($params);

        return $connection;
    }
);

/**
 * Register the logging service for debugging
 */
$di->setShared(
    'logger', function () {
        $config = $this->getConfig();
        return new Logger(
            'messages',
            [
                'main' => new Stream($config->application->logsDir . 'application.log')
            ]
        );
    }
);
