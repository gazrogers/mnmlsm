<?php
namespace Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

use Library\Exceptions\UnsupportedMediaType;

class FormatCheck implements MiddlewareInterface
{
    public function beforeExecuteRoute(Event $event, Micro $app)
    {
        if($app->di->get('request')->isPost())
        {
            json_decode($app->di->get('request')->getRawBody());
            if(json_last_error() !== JSON_ERROR_NONE)
            {
                throw new UnsupportedMediaType("Service requests should be sent in JSON");

                return false;
            }
        }

        return true;
    }

    public function call(Micro $app)
    {
        return true;
    }
}