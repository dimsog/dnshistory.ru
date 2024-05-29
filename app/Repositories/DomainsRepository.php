<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entity\Domain;
use App\Utils\DomainTableResolver;
use App\ValueObjects\Domain as DomainName;
use App\ValueObjects\DomainId;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class DomainsRepository
{
    public function add(DomainName $domain): Domain
    {
        $id = $this->getQuery($domain)
            ->insertGetId([
                'name' => $domain->name,
            ]);
        return new Domain(
            id: new DomainId($id),
            domain: $domain,
        );
    }

    public function countByZone(string $zone): int
    {
        return $this->getQueryByZone($zone)->count();
    }

    public function findAllForImportDnsJob(string $zone, \Closure $callback): void
    {
        $this->getQueryByZone($zone)
            ->orderBy('id')
            ->chunk(5000, static function (Collection $rawDomains) use ($callback): void {
                $domains = [];
                foreach ($rawDomains as $rawDomain) {
                    $domains[] = new Domain(
                        id: new DomainId((int) $rawDomain->id),
                        domain: new DomainName($rawDomain->name),
                    );
                }
                $callback($domains);
        });
    }

    public function exists(DomainName $domain): bool
    {
        return $this->getQuery($domain)
            ->where('name', $domain->name)
            ->exists();
    }

    public function findOrCreate(DomainName $domain): Domain
    {
        $domainEntity = $this->findDomain($domain);
        if ($domainEntity !== null) {
            return $domainEntity;
        }
        return $this->add($domain);
    }

    public function findDomain(DomainName $domain): ?Domain
    {
        $rawDomain = $this->getQuery($domain)
            ->where('name', $domain->name)
            ->first();

        if ($rawDomain === null) {
            return null;
        }

        return new Domain(
            new DomainId((int) $rawDomain->id),
            new DomainName($rawDomain->name),
        );
    }

    private function getQuery(DomainName $domain): Builder
    {
        return DB::table(DomainTableResolver::resolve($domain));
    }

    private function getQueryByZone(string $zone): Builder
    {
        return DB::table(DomainTableResolver::resolveByZone($zone));
    }
}
