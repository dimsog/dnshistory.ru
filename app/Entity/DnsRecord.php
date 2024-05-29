<?php

declare(strict_types=1);

namespace App\Entity;

final class DnsRecord
{
    public function __construct(
        public readonly int $dnsId,
        public readonly string $type,
        public readonly string $class,
        public readonly string $value,
    ) {
    }
}
