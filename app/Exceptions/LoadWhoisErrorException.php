<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

final class LoadWhoisErrorException extends RuntimeException
{
    public function __construct(string $message = "При загрузке whois произошла ошибка", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
