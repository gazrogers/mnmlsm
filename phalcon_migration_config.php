<?php
use Phalcon\Config;

return new Config (
    [
        'database' => [
            'adapter'  => 'mysql',
            'host'     => getenv('DB_HOST'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'dbname'   => getenv('DB_PREFIX') . 'mnmlsm',
            'charset'  => 'utf8',
        ],
        'application' => [
            'logInDb' => true,
            'migrationsDir' => 'db/migrations',
            'migrationsTsBased' => true,
            'exportDataFromTables' => [
                // List names of tables that have fixed constant values defined by us in here
            ]
        ]
    ]
);
