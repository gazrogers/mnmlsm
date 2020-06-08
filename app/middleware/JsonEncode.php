<?php
namespace Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

class JsonEncode implements MiddlewareInterface
{
    public function call(Micro $app)
    {
        $app->response->setStatusCode(200, 'OK');
        $app->response->setJsonContent($app->getReturnedValue());
        $app->response->send();

        return true;
    }
}
