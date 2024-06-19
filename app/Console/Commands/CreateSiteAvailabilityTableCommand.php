<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DomainZone;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateSiteAvailabilityTableCommand extends Command
{
    protected $signature = 'app:create-site-availability';

    protected $description = '';


    public function handle(): void
    {
        $zones = DomainZone::all();
        foreach ($zones as $zone) {
            $this->output->writeln($zone->name);
            foreach ($this->getLetters() as $letter) {
                if (!Schema::hasTable("{$zone->name}_{$letter}_site_availability")) {
                    Schema::create("{$zone->name}_{$letter}_site_availability", static function (Blueprint $table): void {
                        $table->integer('id')->autoIncrement();
                        $table->integer('domain_id')->index();
                        $table->integer('date');
                        $table->integer('status');
                        $table->integer('latency');
                    });
                }
            }
        }
    }

    private function getLetters(): array
    {
        return array_map(fn ($item) => (string) $item, [...range(0, 9), ...range('a', 'z')]);
    }
}
