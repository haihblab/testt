<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class UnknownException extends AbstractException
{
    public function __construct($message = '', $code = null)
    {
        if (!$message) {
            $message = trans('exception.bad_request');
        }
        if (!$code) {
            $code = Response::HTTP_BAD_REQUEST;
        }
        parent::__construct($message, $code);
    }
}
