<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\WhoisLoadingResult;
use Illuminate\Support\Facades\Log;
use Iodev\Whois\Exceptions\ConnectionException;
use App\ValueObjects\Domain;
use DateTimeImmutable;
use App\Entity\Whois;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Factory;
use Iodev\Whois\Loaders\CurlLoader;
use Iodev\Whois\Loaders\SocketLoader;

final class WhoisLoader
{
    /**
     * @param Domain $domain
     * @return Whois|null
     */
    public function load(Domain $domain): WhoisLoadingResult
    {
        try {
            $whois = Factory::get()->createWhois(
                new CurlLoader(5),
            );

            $domainInfo = $whois->loadDomainInfo($domain->getRootDomain());

            if (empty($domainInfo)) {
                return new WhoisLoadingResult(
                    whois: null,
                );
            }

            return new WhoisLoadingResult(
                whois: new Whois(
                    createdAt: (new DateTimeImmutable())->setTimestamp($domainInfo->creationDate),
                    paidTill: (new DateTimeImmutable())->setTimestamp($domainInfo->expirationDate),
                    registrar: $domainInfo->registrar,
                    nameServers: $domainInfo->nameServers,
                    states: $domainInfo->states,
                ),
            );
        } catch (ConnectionException|ServerMismatchException|WhoisException $e) {
            Log::error($e->getMessage(), [
                'exception' => $e,
            ]);
            return new WhoisLoadingResult(
                whois: null,
                errorText: 'При загрузке whois произошла ошибка',
            );
        }
    }
}
