<?php

declare(strict_types=1);

namespace App\ValueObjects;

final class DomainId
{
    public function __construct(
        public int $value,
    ) {
    }
}
