<?php

declare(strict_types=1);

namespace App\Utils;

use App\ValueObjects\Domain;

final class DomainTableResolver
{
    public static function resolve(Domain $domain): string
    {
        return self::resolveByZone($domain->getZone());
    }

    public static function resolveByZone(string $zone): string
    {
        return "{$zone}_domains";
    }
}
