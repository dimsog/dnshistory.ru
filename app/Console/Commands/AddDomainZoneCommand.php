<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Repositories\DomainZonesRepository;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class AddDomainZoneCommand extends Command
{
    protected $signature = 'app:add-zone {zone}';

    protected $description = 'Добавление поддержки новой доменной зоны';


    public function handle(DomainZonesRepository $domainZonesRepository): void
    {
        $zone = $this->getZone();
        $this->output->writeln("Create domains table");
        if (!Schema::hasTable("{$zone}_domains")) {
            Schema::create("{$zone}_domains", static function (Blueprint $table): void {
                $table->integer('id')->autoIncrement();
                $table->string('name')->unique();
            });
        }

        $this->output->writeln("Create dns table");
        foreach ($this->getLetters() as $letter) {
            if (!Schema::hasTable("{$zone}_{$letter}_dns")) {
                Schema::create("{$zone}_{$letter}_dns", static function (Blueprint $table): void {
                    $table->integer('id')->autoIncrement();
                    $table->integer('domain_id')->index();
                    $table->integer('date');
                    $table->string('hash', 8);

                    $table->unique(['domain_id', 'hash']);
                });
            }
        }

        $this->output->writeln("Create dns records table");
        foreach ($this->getLetters() as $letter) {
            if (!Schema::hasTable("{$zone}_{$letter}_dns_records")) {
                Schema::create("{$zone}_{$letter}_dns_records", static function (Blueprint $table): void {
                    $table->integer('dns_id')->index();
                    $table->string('type', 6);
                    $table->string('class', 6);
                    $table->string('value', 128)->nullable();
                });
            }
        }

        $this->output->writeln('Create site availability table');
        foreach ($this->getLetters() as $letter) {
            if (!Schema::hasTable("{$zone}_{$letter}_site_availability")) {
                Schema::create("{$zone}_{$letter}_site_availability", static function (Blueprint $table): void {
                    $table->integer('id')->autoIncrement();
                    $table->integer('domain_id')->index();
                    $table->integer('date');
                    $table->integer('status');
                    $table->integer('latency');
                });
            }
        }

        if (!$domainZonesRepository->exists($zone)) {
            $domainZonesRepository->add($zone);
        }
    }

    private function getZone(): string
    {
        return $this->argument('zone');
    }

    private function getLetters(): array
    {
        return array_map(fn ($item) => (string) $item, [...range(0, 9), ...range('a', 'z')]);
    }
}
