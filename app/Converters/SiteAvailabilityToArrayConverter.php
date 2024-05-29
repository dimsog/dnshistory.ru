<?php

declare(strict_types=1);

namespace App\Converters;

use App\Entity\SiteAvailability;

final class SiteAvailabilityToArrayConverter
{
    public function convert(SiteAvailability $siteAvailability): array
    {
        return [
            'date' => $siteAvailability->date->format('Y-m-d'),
            'latency' => $siteAvailability->latency,
            'status' => $siteAvailability->status,
        ];
    }
}
