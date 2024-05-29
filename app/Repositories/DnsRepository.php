<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entity\Dns;
use App\Entity\Domain;
use App\Factory\DnsFactory;
use App\Utils\DnsTableResolver;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use App\ValueObjects\Domain as DomainName;
use Illuminate\Support\Facades\DB;
use Throwable;

final class DnsRepository
{
    public function __construct(
        private readonly DnsRecordsRepository $dnsRecordsRepository,
    ) {
    }

    /**
     * @param Domain $domain
     * @param Dns $dns
     * @return void
     * @throws Throwable
     */
    public function addOrIgnore(Domain $domain, Dns $dns): void
    {
        if ($this->existsByHash($domain, $dns->hash) || $dns->records->count() == 0) {
            return;
        }
        try {
            DB::beginTransaction();
            $dnsId = $this->getQuery($domain->domain)
                ->insertGetId([
                    'domain_id' => $domain->id->value,
                    'date' => time(),
                    'hash' => $dns->hash,
                ]);
            $this->dnsRecordsRepository->addRecords(
                domain: $domain->domain,
                dnsId: $dnsId,
                dnsRecords: $dns->records,
            );
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function existsByHash(Domain $domain, string $hash): bool
    {
        return $this->getQuery($domain->domain)
            ->where('domain_id', $domain->id->value)
            ->where('hash', $hash)
            ->exists();
    }

    /**
     * @param Domain $domain
     * @return Collection<Dns>|Dns[]
     */
    public function findAllByDomain(Domain $domain): Collection
    {
        return $this->getQuery($domain->domain)
            ->where('domain_id', $domain->id->value)
            ->orderByDesc('id')
            ->get()
            ->map(function (\stdClass $rawDns) use ($domain): Dns {
                return DnsFactory::getInstance(
                    rawDns: $rawDns,
                    dnsRecords: $this->dnsRecordsRepository->findAll(
                        domain: $domain->domain,
                        dnsId: (int) $rawDns->id,
                    ),
                );
            });
    }

    private function getQuery(DomainName $domain): Builder
    {
        return DB::table(DnsTableResolver::resolve($domain));
    }
}
