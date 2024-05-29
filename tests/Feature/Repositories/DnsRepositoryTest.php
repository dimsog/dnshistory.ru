<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Entity\DnsRecord;
use DateTimeImmutable;
use App\Entity\Dns;
use App\Repositories\DnsRepository;
use App\Repositories\DomainsRepository;
use App\ValueObjects\Domain;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class DnsRepositoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('ru_domains')
            ->delete();
        DB::table('ru_d_dns_records')
            ->delete();
        DB::table('ru_d_dns')
            ->delete();
    }

    public function test_find_all(): void
    {
        /** @var DomainsRepository $domainsRepository */
        $domainsRepository = $this->app->make(DomainsRepository::class);
        /** @var DnsRepository $dnsRepository */
        $dnsRepository = $this->app->make(DnsRepository::class);
        $domain = $domainsRepository->add(new Domain('dnshistory.ru'));
        $domain = $domainsRepository->findDomain(new Domain('dnshistory.ru'));

        $dnsId = DB::table('ru_d_dns')
            ->insertGetId([
                'domain_id' => $domain->id->value,
                'date' => $date = time(),
                'hash' => '12345678',
            ]);

        DB::table('ru_d_dns_records')
            ->insert([
                [
                    'dns_id' => $dnsId,
                    'type' => 'A',
                    'class' => 'IN',
                    'value' => '127.0.0.1',
                ],
            ]);

        $dns2Id = DB::table('ru_d_dns')
            ->insertGetId([
                'domain_id' => $domain->id->value,
                'date' => now()->addDays(5)->getTimestamp(),
                'hash' => '8765421',
            ]);

        DB::table('ru_d_dns_records')
            ->insert([
                [
                    'dns_id' => $dns2Id,
                    'type' => 'A',
                    'class' => 'IN',
                    'value' => '127.0.0.1',
                ],
            ]);


        $dnsItems = $dnsRepository->findAllByDomain($domain);

        $this->assertCount(2, $dnsItems);
        $this->assertSame($dnsId, $dnsItems[1]->id);
        $this->assertSame($date, $dnsItems[1]->date->getTimestamp());
        $this->assertSame('12345678', $dnsItems[1]->hash);

        $this->assertSame('A', $dnsItems[1]->records[0]->type);
        $this->assertSame('IN', $dnsItems[1]->records[0]->class);
        $this->assertSame('127.0.0.1', $dnsItems[1]->records[0]->value);
    }

    public function test_add_or_ignore_dns_exists(): void
    {
        /** @var DomainsRepository $domainsRepository */
        $domainsRepository = $this->app->make(DomainsRepository::class);
        /** @var DnsRepository $dnsRepository */
        $dnsRepository = $this->app->make(DnsRepository::class);

        $domain = $domainsRepository->add(new Domain('dnshistory.ru'));
        $dnsId = DB::table('ru_d_dns')
            ->insertGetId([
                'domain_id' => $domain->id->value,
                'date' => time(),
                'hash' => '12345678',
            ]);

        DB::table('ru_d_dns_records')
            ->insert([
                [
                    'dns_id' => $dnsId,
                    'type' => 'A',
                    'class' => 'IN',
                    'value' => '127.0.0.1',
                ],
            ]);

        $dns = new Dns(
            id: 0,
            date: new DateTimeImmutable(),
            hash: '12345678',
            records: collect([
                new DnsRecord(
                    dnsId: 0,
                    type: 'A',
                    class: 'IN',
                    value: '127.0.0.1',
                )
            ])
        );

        $dnsRepository->addOrIgnore($domain, $dns);
        $this->assertDatabaseCount('ru_d_dns', 1);
        $this->assertDatabaseCount('ru_d_dns_records', 1);
    }

    public function test_add_or_ignore_records_is_empty(): void
    {
        /** @var DomainsRepository $domainsRepository */
        $domainsRepository = $this->app->make(DomainsRepository::class);
        /** @var DnsRepository $dnsRepository */
        $dnsRepository = $this->app->make(DnsRepository::class);

        $domain = $domainsRepository->add(new Domain('dnshistory.ru'));

        $dns = new Dns(
            id: 0,
            date: new DateTimeImmutable(),
            hash: '12345678',
            records: collect(),
        );

        $dnsRepository->addOrIgnore($domain, $dns);
        $this->assertDatabaseCount('ru_d_dns', 0);
        $this->assertDatabaseCount('ru_d_dns_records', 0);
    }

    public function test_add_dns_success(): void
    {
        /** @var DomainsRepository $domainsRepository */
        $domainsRepository = $this->app->make(DomainsRepository::class);
        /** @var DnsRepository $dnsRepository */
        $dnsRepository = $this->app->make(DnsRepository::class);

        $domain = $domainsRepository->add(new Domain('dnshistory.ru'));
        $dnsId = DB::table('ru_d_dns')
            ->insertGetId([
                'domain_id' => $domain->id->value,
                'date' => time(),
                'hash' => '12345678',
            ]);

        DB::table('ru_d_dns_records')
            ->insert([
                [
                    'dns_id' => $dnsId,
                    'type' => 'A',
                    'class' => 'IN',
                    'value' => '127.0.0.1',
                ],
            ]);

        $dns = new Dns(
            id: 0,
            date: new DateTimeImmutable(),
            hash: 'xxxxxxxx',
            records: collect([
                new DnsRecord(
                    dnsId: 0,
                    type: 'A',
                    class: 'IN',
                    value: '127.0.0.1',
                ),
                new DnsRecord(
                    dnsId: 0,
                    type: 'A',
                    class: 'IN',
                    value: '127.0.0.2',
                )
            ])
        );

        $dnsRepository->addOrIgnore($domain, $dns);
        $this->assertDatabaseCount('ru_d_dns', 2);
        $this->assertDatabaseCount('ru_d_dns_records', 3);
    }
}
