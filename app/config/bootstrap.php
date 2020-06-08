<?php
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

$_SERVER['REQUEST_URI'] = '';

$di = new \Phalcon\DI\FactoryDefault();
include __DIR__ . '/services.php';
$config = $di->getConfig();
include __DIR__ . '/loader.php';
$app = new \Phalcon\Mvc\Micro($di);
include __DIR__ . '/../app.php';

return $app;
