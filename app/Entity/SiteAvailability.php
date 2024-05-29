<?php

declare(strict_types=1);

namespace App\Entity;

use Carbon\CarbonInterface;

final class SiteAvailability
{
    public function __construct(
        public readonly int $domainId,
        public readonly CarbonInterface $date,
        public readonly int $status,
        public readonly int $latency,
    ) {
    }
}
