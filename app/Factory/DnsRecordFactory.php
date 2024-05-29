<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\DnsRecord;

final class DnsRecordFactory
{
    public static function getInstance(\stdClass $rawDnsRecord): DnsRecord
    {
        return new DnsRecord(
            dnsId: (int) $rawDnsRecord->dns_id,
            type: (string) $rawDnsRecord->type,
            class: (string) $rawDnsRecord->class,
            value: (string) $rawDnsRecord->value,
        );
    }
}
