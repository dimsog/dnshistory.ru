<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Whois;

final class WhoisLoadingResult
{
    public function __construct(
        public readonly ?Whois $whois,
        public readonly ?string $errorText = null
    ) {
    }
}
