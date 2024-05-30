<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Repositories\DnsRepository;
use App\Repositories\DomainsRepository;
use App\ValueObjects\Domain;
use RuntimeException;
use App\Services\DnsLoader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

final class DnsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;

    public int $backoff = 5;


    /**
     * @param Domain $domain
     */
    public function __construct(
        public readonly Domain $domain,
    ) {
    }

    public function handle(
        DnsLoader $dnsLoader,
        DomainsRepository $domainsRepository,
        DnsRepository $dnsRepository,
    ): void {
        try {
            $domain = $domainsRepository->findDomain($this->domain);
            $dns = $dnsLoader->load($domain);
            if ($dns !== null) {
                $dnsRepository->addOrIgnore(
                    domain: $domain,
                    dns: $dns,
                );
            }
        } catch (RuntimeException) {
            // nothing
            return;
        }
    }
}
