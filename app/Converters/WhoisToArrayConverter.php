<?php

declare(strict_types=1);

namespace App\Converters;

use App\Entity\Whois;

final class WhoisToArrayConverter
{
    public function convert(Whois $whois): array
    {
        return [
            'created_at' => $whois->createdAt->format('Y-m-d'),
            'created_at_ru' => $whois->createdAt->format('d.m.Y'),
            'paid_till' => $whois->paidTill->format('Y-m-d'),
            'paid_till_ru' => $whois->paidTill->format('d.m.Y'),
            'registrar' => $whois->registrar,
            'name_servers' => $whois->nameServers,
            'states' => $whois->states,
        ];
    }
}
