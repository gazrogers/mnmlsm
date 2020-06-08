<?php
namespace Model\BusinessLogic;

use Error;
use Exception;
use Phalcon\Di\Injectable;
use Library\Exceptions\HttpError;

class ErrorHandler extends Injectable
{
    public function handle($exception)
    {
        $response = $this->di->get('response');
        if($exception instanceof HttpError)
        {
            $response->setStatusCode($exception->httpErrorCode, $exception->httpErrorType);
            $response->setJsonContent(
                [
                    'errors' => [
                        ['message' => $exception->getMessage()]
                    ]
                ]
            );
        }
        elseif($exception instanceof Exception)
        {
            $response->setStatusCode(500, 'Internal Server Error');
            $response->setJsonContent(
                [
                    'errors' => [
                        ['message' => $exception->getMessage()]
                    ]
                ]
            );
            $this->di->get('logger')->error($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        }
        else
        {
            $response->setStatusCode(500, 'Internal Server Error');
            $response->setJsonContent(
                [
                    'errors' => [
                        ['message' => 'Server error']
                    ]
                ]
            );
            $this->di->get('logger')->error(
                $exception instanceof Error ? $exception->getMessage() : "Unhandled error class: " . get_class($exception)
            );
        }
    }
}
