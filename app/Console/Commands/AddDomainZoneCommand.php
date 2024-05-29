<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Repositories\DomainZonesRepository;
use App\Services\AddDomainZoneService;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class AddDomainZoneCommand extends Command
{
    protected $signature = 'app:add-zone {zone}';

    protected $description = 'Добавление поддержки новой доменной зоны';


    public function handle(AddDomainZoneService $addDomainZoneService): void
    {
        $addDomainZoneService->add($this->getZone());
    }

    private function getZone(): string
    {
        return $this->argument('zone');
    }


}
