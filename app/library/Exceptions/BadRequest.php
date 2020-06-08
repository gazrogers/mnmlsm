<?php
namespace Library\Exceptions;

class BadRequest extends HttpError
{
    public $httpErrorCode = 400;
    public $httpErrorType = 'Bad Request';
}
