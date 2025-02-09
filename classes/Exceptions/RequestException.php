<?php

namespace Exceptions;

class RequestException extends QuintypeException
{
    protected $errorMessages = [
        1001 => 'Failed to authenticate.',
    ];
}
