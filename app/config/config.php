<?php

/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        'adapter'    => 'Mysql',
        'host'       => getenv('DB_HOST'),
        'username'   => getenv('DB_USER'),
        'password'   => getenv('DB_PASSWORD'),
        'dbname'     => getenv('DB_PREFIX') . 'mnmlsm',
        'charset'    => 'utf8',
    ],

    'application' => [
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'libraryDir'     => APP_PATH . '/library/',
        'middlewareDir'  => APP_PATH . '/middleware/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'logsDir'        => APP_PATH . '/logs/',
        'baseUri'        => '/mmnslm/',
        'cache' => [
            'viewsDir' => BASE_PATH . '/cache/views/'
        ]
    ],
]);
