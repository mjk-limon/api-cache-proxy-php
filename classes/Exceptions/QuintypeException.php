<?php

namespace Exceptions;

use Exception;

abstract class QuintypeException extends Exception
{
    protected $errorMessages = [];

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
