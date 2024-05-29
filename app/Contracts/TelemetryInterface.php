<?php

declare(strict_types=1);

namespace App\Contracts;

interface TelemetryInterface
{
    public function exception(\Throwable $e): void;

    public function send(string $message): void;
}
