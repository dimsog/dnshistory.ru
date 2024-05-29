<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Illuminate\Support\Collection;

final class Dns
{
    public function __construct(
        public readonly int $id,
        public DateTimeImmutable $date,
        public string $hash,
        /**
         * @var Collection<DnsRecord>|DnsRecord[]
         */
        public readonly Collection $records,
    ) {
    }
}
