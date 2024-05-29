<?php

declare(strict_types=1);

namespace App\Entity;

use App\ValueObjects\DomainId;
use App\ValueObjects\Domain as DomainName;

final class Domain
{
    public function __construct(
        public readonly DomainId $id,
        public readonly DomainName $domain,
    ) {
    }
}
