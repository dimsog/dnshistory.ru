<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use InvalidArgumentException;
use App\ValueObjects\Domain;
use Tests\TestCase;

final class DomainTest extends TestCase
{
    public function test_ru_domain(): void
    {
        $domain = new Domain('https://dnshistory.ru');
        $this->assertEquals('dnshistory.ru', $domain->name);
        $this->assertEquals('ru', $domain->getZone());
        $this->assertEquals('ru', $domain->getZone());
        $this->assertEquals('d', $domain->getFirstLetter());
        $this->assertFalse($domain->isCyrillic());
    }

    public function test_cyrillic_domain(): void
    {
        $domain = new Domain('https://уходзацветами.рф');
        $this->assertEquals('xn--80aafghjl5al9beyr.xn--p1ai', $domain->name);
        $this->assertEquals('rf', $domain->getZone());
        $this->assertEquals('u', $domain->getFirstLetter());
        $this->assertTrue($domain->isCyrillic());
        $this->assertTrue($domain->isPunycode());
    }

    public function test_idn_domain(): void
    {
        $domain = new Domain('http://özgürusta.online/');
        $this->assertSame('xn--zgrusta-80a0d.online', $domain->name);
        $this->assertTrue($domain->isPunycode());
        $this->assertFalse($domain->isCyrillic());
        $this->assertSame('x', $domain->getFirstLetter());

        $domain = new Domain('http://beeclã.agency');
        $this->assertSame('xn--beecl-era.agency', $domain->name);
        $this->assertTrue($domain->isPunycode());
        $this->assertFalse($domain->isCyrillic());
        $this->assertSame('x', $domain->getFirstLetter());

        $domain = new Domain('xn--beecl-era.agency');
        $this->assertSame('xn--beecl-era.agency', $domain->name);
        $this->assertTrue($domain->isPunycode());
        $this->assertFalse($domain->isCyrillic());
        $this->assertSame('x', $domain->getFirstLetter());
    }

    public function test_invalid_domain(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Domain('http://еб123');
    }

    public function test_domain_with_uri(): void
    {
        $domain = new Domain('https://dnshistory.ru/foo/bar');
        $this->assertEquals('dnshistory.ru', $domain->name);
        $this->assertEquals('ru', $domain->getZone());
        $this->assertEquals('ru', $domain->getZone());
        $this->assertEquals('d', $domain->getFirstLetter());
        $this->assertFalse($domain->isCyrillic());
    }

    public function test_sub_domain(): void
    {
        $domain = new Domain('https://www.test.dnshistory.ru/foo/bar');
        $this->assertEquals('www.test.dnshistory.ru', $domain->name);
        $this->assertEquals('dnshistory.ru', $domain->getRootDomain());
        $this->assertEquals('ru', $domain->getZone());
        $this->assertEquals('w', $domain->getFirstLetter());
        $this->assertFalse($domain->isCyrillic());
    }

    public function test_rf_site_idn(): void
    {
        $domain = new Domain('http://xn--i1afg.xn--80aswg/ывфыв');
        $this->assertEquals('xn--i1afg.xn--80aswg', $domain->name);
        $this->assertEquals('rf_site', $domain->getZone());
        $this->assertEquals('m', $domain->getFirstLetter());
        $this->assertTrue($domain->isCyrillic());
    }

    public function test_rf_site(): void
    {
        $domain = new Domain('http://мой.сайт/ывфыв');
        $this->assertEquals('xn--i1afg.xn--80aswg', $domain->name);
        $this->assertEquals('rf_site', $domain->getZone());
        $this->assertEquals('m', $domain->getFirstLetter());
        $this->assertTrue($domain->isCyrillic());
    }

    public function test_with_slash_in_the_end(): void
    {
        $domain = new Domain('test.ru/');
        $this->assertSame('test.ru', $domain->name);
    }

    public function test_with_url_without_protocol(): void
    {
        $domain = new Domain('test.ru/url/test/');
        $this->assertSame('test.ru', $domain->name);
    }

    public function test_with_url_and_protocol(): void
    {
        $domain = new Domain('http://test.ru/url/test/');
        $this->assertSame('test.ru', $domain->name);
    }
}
