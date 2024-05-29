<?php

declare(strict_types=1);

namespace App\Utils;

use App\ValueObjects\Domain;

/**
 * Класс резолвит название таблицы, куда по домену сохранять dns
 */
final class DnsTableResolver
{
    public static function resolve(Domain $domain): string
    {
        return $domain->getZone() . '_' . $domain->getFirstLetter() . '_dns';
    }
}
