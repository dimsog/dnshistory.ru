<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\DomainZonesRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class AddDomainZoneService
{
    public function __construct(
        private readonly DomainZonesRepository $domainZonesRepository,
    ) {
    }

    public function add(string $zone)
    {
        if (!Schema::hasTable("{$zone}_domains")) {
            Schema::create("{$zone}_domains", static function (Blueprint $table): void {
                $table->integer('id')->autoIncrement();
                $table->string('name')->unique();
            });
        }

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

        if (!$this->domainZonesRepository->exists($zone)) {
            $this->domainZonesRepository->add($zone);
        }
    }

    private function getLetters(): array
    {
        return array_map(fn ($item) => (string) $item, [...range(0, 9), ...range('a', 'z')]);
    }
}
