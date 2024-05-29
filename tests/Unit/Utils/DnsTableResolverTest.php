<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Utils\DnsTableResolver;
use App\ValueObjects\Domain;
use Tests\TestCase;

final class DnsTableResolverTest extends TestCase
{
    public function test_resolve(): void
    {
        $domain = new Domain('dnshistory.ru');
        $this->assertSame('ru_d_dns', DnsTableResolver::resolve($domain));
    }

    public function test_resolve_cyrillic(): void
    {
        $domain = new Domain('тест.рф');
        $this->assertSame('rf_t_dns', DnsTableResolver::resolve($domain));
    }
}
