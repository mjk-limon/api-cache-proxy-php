<?php

namespace Exceptions;

class RequestException extends QuintypeException
{
    protected $errorMessages = [
        400 => 'Bad request.',
        403 => 'Forbidden',
        1001 => 'Failed to authenticate.',
        1003 => 'Rate Limit Exceed.',
    ];
}
