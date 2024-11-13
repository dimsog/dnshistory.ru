<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\LoadWhoisErrorException;
use Illuminate\Support\Facades\Log;
use Iodev\Whois\Exceptions\ConnectionException;
use App\ValueObjects\Domain;
use DateTimeImmutable;
use App\Entity\Whois;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Whois as BaseWhoisLoader;

final class WhoisLoader
{
    public function __construct(
        private readonly BaseWhoisLoader $baseWhoisLoader,
    ) {
    }

    /**
     * @param Domain $domain
     * @return Whois
     */
    public function load(Domain $domain): Whois
    {
        try {
            $domainInfo = $this->baseWhoisLoader->loadDomainInfo($domain->getRootDomain());

            if ($domainInfo === null) {
                throw new LoadWhoisErrorException("Не удалось загрузить информацию по домену");
            }

            $createdAt = null;
            $creationDate = $domainInfo->creationDate;
            if (!empty($creationDate)) {
                $createdAt = (new DateTimeImmutable())->setTimestamp($creationDate);
            }

            return new Whois(
                createdAt: $createdAt,
                paidTill: $domainInfo->expirationDate > 0 ? (new DateTimeImmutable())->setTimestamp($domainInfo->expirationDate) : null,
                registrar: $domainInfo->registrar,
                nameServers: $domainInfo->nameServers,
                states: $domainInfo->states,
            );
        } catch (ConnectionException|ServerMismatchException|WhoisException $e) {
            Log::error($e->getMessage(), [
                'exception' => $e,
            ]);
            throw new LoadWhoisErrorException();
        }
    }
}
