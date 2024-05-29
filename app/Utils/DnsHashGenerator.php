<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Dns;
use App\Entity\DnsRecord;
use Illuminate\Support\Collection;

final class DnsHashGenerator
{
    /**
     * @param Collection<DnsRecord>|DnsRecord[] $dnsRecords
     * @return string
     */
    public static function generate(Collection $dnsRecords): string
    {
        $txt = '';
        foreach ($dnsRecords as $dnsRecord) {
            $txt .= $dnsRecord->type . $dnsRecord->class . $dnsRecord->value;
        }
        return substr(md5($txt), 0, 8);
    }
}
