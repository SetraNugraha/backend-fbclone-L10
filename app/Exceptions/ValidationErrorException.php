<?php

namespace App\Exceptions;

use Exception;

class ValidationErrorException extends Exception
{
    protected array $errors;
    protected string $messageText;
    protected int $statusCode;

    public function __construct(array $errors, string $message = "validation error", int $code = 400)
    {
        parent::__construct($message, $code);

        $this->errors = $errors;
        $this->messageText = $message;
        $this->statusCode = $code;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getMessageText(): string
    {
        return $this->messageText;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
