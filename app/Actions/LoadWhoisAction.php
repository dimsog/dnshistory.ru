<?php

declare(strict_types=1);

namespace App\Actions;

use App\Entity\Whois;
use App\Exceptions\LoadWhoisErrorException;
use App\Services\WhoisLoader;
use App\ValueObjects\Domain;

final class LoadWhoisAction
{
    public function __construct(
        private readonly WhoisLoader $whoisLoader,
    ) {
    }

    /**
     * @param Domain $domain
     * @return array{
     *   whois: Whois|null,
     *   errorText: string|null
     * }
     */
    public function handle(Domain $domain): array
    {
        try {
            return [$this->whoisLoader->load($domain), null];
        } catch (LoadWhoisErrorException $e) {
            return [null, $e->getMessage()];
        }
    }
}
