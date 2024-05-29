<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;
use InvalidArgumentException;

final class DomainNameNotSupportedException extends InvalidArgumentException
{
    public function __construct(string $message = "Доменное имя не поддерживается", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
