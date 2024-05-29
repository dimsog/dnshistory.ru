<?php

declare(strict_types=1);

namespace App\Converters;

use App\Entity\Dns;
use App\Entity\DnsRecord;
use Illuminate\Support\Collection;

final class DnsToArrayConverter
{
    public function convertAll(Collection $dnsItems): array
    {
        return $dnsItems->map(function (Dns $dns): array {
            return $this->convert($dns);
        })->toArray();
    }

    public function convert(Dns $dns): array
    {
        return [
            'date' => $dns->date->getTimestamp(),
            'ru_date' => $dns->date->format('d.m.Y'),
            'records' => $dns->records->map(static function (DnsRecord $record) {
                return [
                    'class' => $record->class,
                    'type' => $record->type,
                    'value' => $record->value,
                ];
            })->toArray(),
        ];
    }
}
