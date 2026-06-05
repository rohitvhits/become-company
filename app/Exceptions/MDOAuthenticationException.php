<?php

namespace App\Exceptions;

use Exception;

class MDOAuthenticationException extends Exception
{
    // Optional: You can customize the constructor for better messages or codes
    public function __construct(string $message = "MDO Authentication Error", int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}