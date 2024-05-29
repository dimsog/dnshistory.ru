<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Throwable;
use App\Entity\DnsRecord;
use App\Factory\DnsRecordFactory;
use App\Utils\DnsRecordTableResolver;
use App\ValueObjects\Domain;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

final class DnsRecordsRepository
{
    /**
     * @param Domain $domain
     * @param int $dnsId
     * @return Collection<DnsRecord>|DnsRecord[]
     */
    public function findAll(Domain $domain, int $dnsId): Collection
    {
        return $this->getQuery($domain)
            ->where('dns_id', $dnsId)
            ->get()
            ->map(static fn (\stdClass $rawDnsRecord) => DnsRecordFactory::getInstance($rawDnsRecord));
    }

    /**
     * @param Domain $domain
     * @param int $dnsId
     * @param Collection $dnsRecords
     * @return void
     * @throws Throwable
     */
    public function addRecords(Domain $domain, int $dnsId, Collection $dnsRecords): void
    {
        try {
            DB::beginTransaction();
            foreach ($dnsRecords as $dnsRecord) {
                $this->addRecord(
                    domain: $domain,
                    dnsId: $dnsId,
                    dnsRecord: $dnsRecord,
                );
            }
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function addRecord(Domain $domain, int $dnsId, DnsRecord $dnsRecord): void
    {
        $this->getQuery($domain)
            ->insert([
                'dns_id' => $dnsId,
                'type' => $dnsRecord->type,
                'class' => $dnsRecord->class,
                'value' => $dnsRecord->value,
            ]);
    }

    private function getQuery(Domain $domain): Builder
    {
        return DB::table(DnsRecordTableResolver::resolve($domain));
    }
}
