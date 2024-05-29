<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entity\SiteAvailability;
use App\Utils\SiteAvailabilityTableResolver;
use App\ValueObjects\Domain;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final class SiteAvailabilityRepository
{
    public function add(Domain $domain, SiteAvailability $siteAvailability): void
    {
        $this->getQuery($domain)
            ->insert([
                'domain_id' => $siteAvailability->domainId,
                'date' => $siteAvailability->date->getTimestamp(),
                'status' => $siteAvailability->status,
                'latency' => $siteAvailability->latency,
            ]);
    }

    private function getQuery(Domain $domain): Builder
    {
        return DB::table(SiteAvailabilityTableResolver::resolve($domain));
    }
}
