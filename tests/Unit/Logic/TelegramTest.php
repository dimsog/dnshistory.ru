<?php

declare(strict_types=1);

namespace Tests\Unit\Logic;

use App\Logic\Telegram;
use Mockery\MockInterface;
use TelegramBot\Api\BotApi;
use Tests\TestCase;

final class TelegramTest extends TestCase
{
    public function test_send_message(): void
    {
        $botApi = $this->mock(BotApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendMessage')->once();
        });
        $telegram = new Telegram(
            $botApi,
            [1]
        );
        $telegram->sendMessage('test');
        $this->assertTrue(true);
    }
}
