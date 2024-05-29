<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Entity\DnsRecord;
use App\Utils\DnsHashGenerator;
use Tests\TestCase;

final class DnsHashGeneratorTest extends TestCase
{
    public function test_generate(): void
    {
        $hash = DnsHashGenerator::generate(collect([
            new DnsRecord(0, 'A', 'IN', '127.0.0.1'),
            new DnsRecord(0, 'TXT', 'IN', 'text-demo'),
        ]));
        $this->assertSame('32c20507', $hash);
    }
}
