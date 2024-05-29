<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Formatter\NormalizerFormatter;
use Monolog\LogRecord;

final class JsonLogFormatter extends NormalizerFormatter
{
    public function format(LogRecord $record): string
    {
        $record = $this->normalize($record->toArray());
        return $this->toJson($record) . PHP_EOL;
    }
}
