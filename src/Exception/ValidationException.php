<?php

namespace Metigan\Exception;

/**
 * Validation Exception
 */
class ValidationException extends \Exception
{
    private ?string $field;

    public function __construct(string $message, ?string $field = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->field = $field;
    }

    public function getField(): ?string
    {
        return $this->field;
    }
}








