<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Repositories\DnsRecordsRepository;
use App\ValueObjects\Domain;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class DnsRecordsRepositoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('ru_d_dns_records')
            ->delete();
    }

    public function test_find_all(): void
    {
        /** @var DnsRecordsRepository $repository */
        $repository = $this->app->make(DnsRecordsRepository::class);

        DB::table('ru_d_dns_records')
            ->insert([
                [
                    'dns_id' => 1,
                    'type' => 'A',
                    'class' => 'IN',
                    'value' => '127.0.0.1',
                ],
                [
                    'dns_id' => 2,
                    'type' => 'A',
                    'class' => 'IN',
                    'value' => '127.0.0.2',
                ],
            ]);

        $dnsRecords = $repository->findAll(new Domain('dnshistory.ru'), 1);
        $this->assertCount(1, $dnsRecords);
        $this->assertEquals(1, $dnsRecords[0]->dnsId);
        $this->assertEquals('A', $dnsRecords[0]->type);
        $this->assertEquals('IN', $dnsRecords[0]->class);
        $this->assertEquals('127.0.0.1', $dnsRecords[0]->value);
    }
}
