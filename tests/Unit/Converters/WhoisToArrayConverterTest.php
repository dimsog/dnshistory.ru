<?php

declare(strict_types=1);

namespace Tests\Unit\Converters;

use App\Converters\WhoisToArrayConverter;
use App\Entity\Whois;
use Tests\TestCase;

final class WhoisToArrayConverterTest extends TestCase
{
    public function test_convert(): void
    {
        $whois = new Whois(
            createdAt: $createdAt = new \DateTimeImmutable('2020-01-01'),
            paidTill: $paidTill = new \DateTimeImmutable('2024-01-01'),
            registrar: 'REG-RU',
            nameServers: [
                'ns1.nic.ru',
                'ns2.nic.ru',
            ],
            states: [
                'REGISTERED',
                'UNVERIFIED',
            ]
        );

        $converter = new WhoisToArrayConverter();
        $whois = $converter->convert($whois);

        $this->assertSame([
            'created_at' => '2020-01-01',
            'created_at_ru' => '01.01.2020',
            'paid_till' => '2024-01-01',
            'paid_till_ru' => '01.01.2024',
            'registrar' => 'REG-RU',
            'name_servers' => ['ns1.nic.ru', 'ns2.nic.ru'],
            'states' => ['REGISTERED', 'UNVERIFIED'],
        ], $whois);
    }
}
