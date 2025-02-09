<?php

namespace Exceptions;

class QuintypeApiException extends QuintypeException
{
    protected $errorMessages = [
        404 => 'API Not Found.',
        1002 => 'Service unavailable.',
    ];
}
