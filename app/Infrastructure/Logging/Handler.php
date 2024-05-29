<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\RotatingFileHandler;

final class Handler extends RotatingFileHandler
{
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new JsonLogFormatter();
    }
}
