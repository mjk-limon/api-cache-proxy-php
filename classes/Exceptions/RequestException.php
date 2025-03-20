<?php

namespace Exceptions;

class RequestException extends QuintypeException
{
    protected $errorMessages = [
        400 => 'Bad request.',
        1001 => 'Failed to authenticate.',
        1003 => 'Rate Limit Exceeded.',
    ];
}
