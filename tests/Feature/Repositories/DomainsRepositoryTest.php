<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Repositories\DomainsRepository;
use App\ValueObjects\Domain;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class DomainsRepositoryTest extends TestCase
{
    use LazilyRefreshDatabase;


    public function test_domain_does_not_exists(): void
    {
        /** @var DomainsRepository $repository */
        $repository = $this->app->make(DomainsRepository::class);

        $this->assertFalse($repository->exists(new Domain('domain-does-not-exits.ru')));
    }

    public function test_domain_exists(): void
    {
        /** @var DomainsRepository $repository */
        $repository = $this->app->make(DomainsRepository::class);
        $repository->add(new Domain('domain-exists.ru'));

        $this->assertTrue($repository->exists(new Domain('domain-exists.ru')));
    }

    public function test_find_domain(): void
    {
        /** @var DomainsRepository $repository */
        $repository = $this->app->make(DomainsRepository::class);

        $repository->add(new Domain('dnshistory.ru'));
        $domain = $repository->findDomain(new Domain('dnshistory.ru'));

        $this->assertEquals('dnshistory.ru', $domain->domain->name);
    }

    public function test_find_domain_does_not_exits(): void
    {
        /** @var DomainsRepository $repository */
        $repository = $this->app->make(DomainsRepository::class);
        $domain = $repository->findDomain(new Domain('dnshistory-does-not-exits.ru'));

        $this->assertNull($domain);
    }

    public function test_find_or_create(): void
    {
        /** @var DomainsRepository $repository */
        $repository = $this->app->make(DomainsRepository::class);
        $this->assertFalse($repository->exists(new Domain('проверка.рф')));

        $domain = $repository->findOrCreate(new Domain('проверка.рф'));
        $domainCreatedId = $domain->id->value;
        $this->assertSame('xn--80adjurfhd.xn--p1ai', $domain->domain->name);

        $domain = $repository->findOrCreate(new Domain('проверка.рф'));
        $domainFoundId = $domain->id->value;
        $this->assertSame('xn--80adjurfhd.xn--p1ai', $domain->domain->name);

        $this->assertSame($domainCreatedId, $domainFoundId);
    }
}
