<?php
namespace Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

use Library\Exceptions\NotFound as NotFoundException;

class NotFound implements MiddlewareInterface
{
    public function beforeNotFound(Event $event, Micro $app)
    {
        $app->di->get('logger')->notice("Request for URI [" . $app->request->getURI() . "] not found");
        throw new NotFoundException("Not Found");
    }

    public function call(Micro $app)
    {
        return true;
    }
}
