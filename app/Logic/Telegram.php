<?php

declare(strict_types=1);

namespace App\Logic;

use TelegramBot\Api\BotApi;

final class Telegram
{
    public function __construct(
        private readonly BotApi $botApi,
        private readonly array $userIds
    ) {
    }

    public function sendMessage(string $message): void
    {
        foreach ($this->userIds as $userId) {
            $this->botApi->sendMessage($userId, $message);
        }
    }
}
