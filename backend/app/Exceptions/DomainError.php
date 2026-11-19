<?php

namespace App\Exceptions;

use Exception;

// Thrown by services for business rule errors, e.g. new DomainError('profileLimitReached', 409).
// bootstrap/app.php turns it into {"message": "...", "code": "..."}.
class DomainError extends Exception
{
    public function __construct(
        public readonly string $errorCode,
        public readonly int $status,
        string $message = '',
    ) {
        parent::__construct($message !== '' ? $message : $errorCode);
    }
}
