<?php

declare(strict_types=1);

namespace App\Services;

use Throwable;
use App\Entity\Domain;
use App\Entity\SiteAvailability;
use Illuminate\Support\Facades\Http;

final class SiteAvailabilityLoader
{
    public function load(Domain $domain): SiteAvailability
    {
        try {
            $start = microtime(true);
            $response = Http::timeout(3)
                ->connectTimeout(1)
                ->get('http://' . $domain->domain->name);
            $latency = microtime(true) - $start;

            return new SiteAvailability(
                domainId: $domain->id->value,
                date: now(),
                status: $response->status(),
                latency: (int)($latency * 1000),
            );
        } catch (Throwable) {
            return new SiteAvailability(
                domainId: $domain->id->value,
                date: now(),
                status: 0,
                latency: 0,
            );
        }
    }
}
