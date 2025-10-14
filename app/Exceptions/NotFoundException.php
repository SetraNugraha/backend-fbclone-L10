<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    protected string $messageText;

    protected int $statusCode;

    public function __construct(string $message = 'resource not found', int $statusCode = 404)
    {
        parent::__construct($message, $statusCode);

        $this->messageText = $message;
        $this->statusCode = $statusCode;
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
