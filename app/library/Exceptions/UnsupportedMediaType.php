<?php
namespace Library\Exceptions;

class UnsupportedMediaType extends HttpError
{
    public $httpErrorCode = 415;
    public $httpErrorType = 'Unsupported Media Type';
}
