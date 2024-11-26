<?php

namespace Minhyung\KoreaDays;

use RuntimeException;
use Throwable;

class ApiException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
