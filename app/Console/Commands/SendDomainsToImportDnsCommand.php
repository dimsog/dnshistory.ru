<?php

namespace App\Console\Commands;

use App\Entity\Domain;
use App\Jobs\DnsJob;
use App\Repositories\DomainsRepository;
use App\Utils\Telemetry;
use Illuminate\Console\Command;

class SendDomainsToImportDnsCommand extends Command
{
    protected $signature = 'app:dns-import {zone}';


    protected $description = 'Добавление доменов на импорт в очередь';


    public function handle(
        DomainsRepository $domainsRepository,
        Telemetry $telemetry,
    ): void {
        $count = $domainsRepository->countByZone($this->getZone());
        $progressBar = $this->output->createProgressBar($count);
        $domainsRepository->findAllForImportDnsJob($this->getZone(), function (array $domains) use ($progressBar) {
            foreach ($domains as $domain) {
                /** @var Domain $domain */
                DnsJob::dispatch($domain->domain)->onQueue($this->getZone() . '_dns');;
                $progressBar->advance();
            }
        });
        $progressBar->finish();
        $telemetry->send("Добавлено {$count} доменов для зоны {$this->getZone()} в очередь");
    }

    private function getZone(): string
    {
        return $this->argument('zone');
    }
}
