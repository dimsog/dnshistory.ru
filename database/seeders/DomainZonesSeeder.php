<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\AddDomainZoneService;
use Illuminate\Database\Seeder;

class DomainZonesSeeder extends Seeder
{
    public function __construct(
        private readonly AddDomainZoneService $addDomainZoneService,
    ) {
    }

    public function run(): void
    {
        $this->addDomainZoneService->add('ru');
        $this->addDomainZoneService->add('rf');
        $this->addDomainZoneService->add('com');
    }
}
