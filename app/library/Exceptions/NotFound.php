<?php
namespace Library\Exceptions;

class NotFound extends HttpError
{
    public $httpErrorCode = 404;
    public $httpErrorType = 'Not Found';
}
