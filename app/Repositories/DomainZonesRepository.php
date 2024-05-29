<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\DomainZone;

final class DomainZonesRepository
{
    public function exists(string $zone): bool
    {
        return DomainZone::where('name', $zone)
            ->exists();
    }

    public function add(string $zone): void
    {
        $model = new DomainZone();
        $model->name = $zone;
        $model->save();
    }
}
