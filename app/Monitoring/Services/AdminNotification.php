<?php

declare(strict_types=1);

namespace App\Monitoring\Services;

interface AdminNotification
{
    public function notify(string $message): void;
}
