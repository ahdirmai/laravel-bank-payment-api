<?php

namespace App\Exceptions;

use Exception;

class PaymentException extends Exception
{
    protected $code;

    public function __construct(string $message, int $code)
    {
        parent::__construct($message);
        $this->code = $code;
    }
}
