<?php
namespace Library\Exceptions;

class Forbidden extends HttpError
{
    public $httpErrorCode = 403;
    public $httpErrorType = 'Forbidden';
}
