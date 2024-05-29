<?php

namespace App\Utils;

use App\ValueObjects\Domain;

/**
 * Класс резолвит название таблицы, куда сохранять записи dns
 */
final class DnsRecordTableResolver
{
    public static function resolve(Domain $domain): string
    {
        return $domain->getZone() . '_' . $domain->getFirstLetter() . '_dns_records';
    }
}
