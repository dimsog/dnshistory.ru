<?php

declare(strict_types=1);

namespace App\Testing;

use App\Contracts\TelemetryInterface;

final class TelemetryMock implements TelemetryInterface
{
    public function exception(\Throwable $e): void
    {
    }

    public function send(string $message): void
    {
    }
}
