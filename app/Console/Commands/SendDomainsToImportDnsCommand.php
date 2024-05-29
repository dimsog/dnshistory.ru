<?php

namespace App\Console\Commands;

use App\Contracts\TelemetryInterface;
use App\Entity\Domain;
use App\Jobs\DnsJob;
use App\Models\DomainZone;
use App\Repositories\DomainsRepository;
use App\Utils\Telemetry;
use Illuminate\Console\Command;

class SendDomainsToImportDnsCommand extends Command
{
    protected $signature = 'app:dns-import';


    protected $description = 'Добавление доменов на импорт в очередь';


    public function handle(
        DomainsRepository $domainsRepository,
        TelemetryInterface $telemetry,
    ): int {
        $zones = DomainZone::findAll();
        foreach ($zones as $zone) {
            $count = $domainsRepository->countByZone($zone->name);
            $progressBar = $this->output->createProgressBar($count);
            $domainsRepository->findAllForImportDnsJob($zone->name, function (array $domains) use ($progressBar) {
                foreach ($domains as $domain) {
                    /** @var Domain $domain */
                    DnsJob::dispatch($domain->domain)->onQueue('dns');
                    $progressBar->advance();
                }
            });
            $progressBar->finish();
            $telemetry->send("Добавлено {$count} доменов для зоны {$zone->name} в очередь");
        }

        return Command::SUCCESS;
    }
}
