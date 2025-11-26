<?php

namespace Minhyung\KoreaDays;

use RuntimeException;
use Throwable;

class ApiException extends RuntimeException
{
    protected int $statusCode;

    public function __construct(string $message = '', int $code = 0, int $statusCode = 200, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
