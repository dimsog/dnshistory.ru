<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Dns;
use App\Entity\DnsRecord;
use App\Entity\Domain;
use Illuminate\Support\Collection;
use DateTimeImmutable;

final class DnsFactory
{
    /**
     * @param \stdClass $rawDns
     * @param Collection<DnsRecord>|DnsRecord[] $dnsRecords
     * @return Dns
     */
    public static function getInstance(\stdClass $rawDns, Collection $dnsRecords): Dns
    {
        return new Dns(
            id: (int) $rawDns->id,
            date: (new DateTimeImmutable())->setTimestamp((int) $rawDns->date),
            hash: (string) $rawDns->hash,
            records: $dnsRecords,
        );
    }
}
