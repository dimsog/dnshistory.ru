<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;

final class Whois
{
    public function __construct(
        public readonly DateTimeImmutable $createdAt,
        public readonly ?DateTimeImmutable $paidTill,
        public readonly string $registrar,
        public readonly array $nameServers,
        public readonly array $states,
    ) {
    }
}
