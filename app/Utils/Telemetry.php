<?php

declare(strict_types=1);

namespace App\Utils;

use App\Monitoring\Services\AdminNotificationService;

final class Telemetry
{
    public function __construct(
        private readonly AdminNotificationService $adminNotificationService,
    ) {
    }

    public function exception(\Throwable $e): void
    {
        $this->adminNotificationService->notify("
Ошибка!
{$e->getMessage()}
File: {$e->getFile()}
Line: {$e->getLine()}
      ");
    }

    public function send(string $message): void
    {
        $this->adminNotificationService->notify($message);
    }
}
