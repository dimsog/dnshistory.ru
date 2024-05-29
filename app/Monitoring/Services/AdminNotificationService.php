<?php

declare(strict_types=1);

namespace App\Monitoring\Services;

use App\Logic\Telegram;

final class AdminNotificationService implements AdminNotification
{
    public function __construct(private readonly Telegram $telegram)
    {
    }

    public function notify(string $message): void
    {
        $this->telegram->sendMessage($message);
    }
}
