<?php

declare(strict_types=1);

namespace App\Utils;

use App\ValueObjects\Domain;

final class SiteAvailabilityTableResolver
{
    public static function resolve(Domain $domain): string
    {
        return $domain->getZone() . '_' . $domain->getFirstLetter() . '_site_availability';
    }
}
