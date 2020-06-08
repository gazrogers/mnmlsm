<?php

use Phalcon\Events\Manager;
use Phalcon\Mvc\Micro\Collection as MicroCollection;
use Middleware\FormatCheck as FormatCheckMiddleware;
use Middleware\JsonEncode as JsonEncodeMiddleware;
use Middleware\NotFound as NotFoundMiddleware;
use Library\Exceptions\NotFound as NotFoundException;

/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

/**
 * Add your routes here
 */

$posts = new MicroCollection();
$posts->setHandler($app->di->get('Controller\\PostController'));
$posts->setPrefix('/post');
// Routes for post manipulation
$posts->post('', 'create');
$posts->get('/{postId:[0-9]*}', 'read');
$posts->delete('/{postId:[0-9]*}', 'delete');
// Routes for manipulation of post likes
$posts->put('/{postId:[0-9]*}/like', 'createLike');
$posts->get('/{postId:[0-9]*}/like', 'readLikes');
$posts->delete('/{postId:[0-9]*}/like', 'removeLike');
$app->mount($posts);

$users = new MicroCollection();
$users->setHandler($app->di->get('Controller\\UserController'));
$users->setPrefix('/user');
$users->post('', 'create');
$users->get('/{postId:[0-9]*}', 'read');
$users->post('/{postId:[0-9]*}', 'update');
$app->mount($users);

/**
 * Middleware
 */

$manager = new Manager();

$manager->attach('micro', new NotFoundMiddleware());
$app->before(new NotFoundMiddleware());

$manager->attach('micro', new FormatCheckMiddleware());
$app->before(new FormatCheckMiddleware());

$manager->attach('micro', new JsonEncodeMiddleware());
$app->after(new JsonEncodeMiddleware());

$app->setEventsManager($manager);


/**
 * Error handler
 */
$app->error(function ($exception) use($app) {
    $errorHandler = $app->di->get('Model\\BusinessLogic\\ErrorHandler');
    $errorHandler->handle($exception);
    $app->response->send();
    return false;
});
