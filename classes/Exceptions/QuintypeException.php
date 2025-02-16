<?php

namespace Exceptions;

use Exception;

class QuintypeException extends Exception
{
    protected $errorMessages = [
        1004 => 'Something went wrong. Please try again later'
    ];

    public function __construct($code, $message = null, Exception $previous = null)
    {
        $message = $message ?? $this->getErrorMessage($code);
        parent::__construct($message, $code, $previous);
    }

    protected function getErrorMessage($code)
    {
        return $this->errorMessages[$code] ?? 'Unknown error.';
    }
}
