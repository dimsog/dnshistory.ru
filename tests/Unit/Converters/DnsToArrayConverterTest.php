<?php

declare(strict_types=1);

namespace Tests\Unit\Converters;

use App\Converters\DnsToArrayConverter;
use App\Entity\Dns;
use App\Entity\DnsRecord;
use Tests\TestCase;

final class DnsToArrayConverterTest extends TestCase
{
    public function test_convert(): void
    {
        $dns = new Dns(
            id: 100,
            date: $date = new \DateTimeImmutable('2024-02-29 14:00:00'),
            hash: '12345678',
            records: collect([
                new DnsRecord(
                    dnsId: 100,
                    type: 'A',
                    class: 'IN',
                    value: '127.0.0.1',
                ),
                new DnsRecord(
                    dnsId: 100,
                    type: 'A',
                    class: 'IN',
                    value: '127.0.0.2',
                ),
            ])
        );

        $converter = new DnsToArrayConverter();
        $dns = $converter->convert($dns);

        $this->assertSame([
            'date' => $date->getTimestamp(),
            'ru_date' => '29.02.2024',
            'records' => [
                [
                    'class' => 'IN',
                    'type' => 'A',
                    'value' => '127.0.0.1',
                ],
                [
                    'class' => 'IN',
                    'type' => 'A',
                    'value' => '127.0.0.2',
                ]
            ]
        ], $dns);
    }
}
