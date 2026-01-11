<?php

namespace Metigan\Exception;

/**
 * API Exception
 */
class ApiException extends \Exception
{
    private int $statusCode;
    private ?string $error;

    public function __construct(string $message, int $statusCode = 0, ?string $error = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->error = $error;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}








