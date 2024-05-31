<?php

namespace App\Console\Commands;

use App\Entity\Domain;
use App\Models\Dns;
use App\Models\DnsRecord;
use App\Models\DomainZone;
use App\Repositories\DnsRepository;
use App\Repositories\DomainsRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateDataCommand extends Command
{
    protected $signature = 'app:migrate-data';

    protected $description = 'Перенести данные на новую структуру';


    public function handle(
        DomainsRepository $domainsRepository,
        DnsRepository $dnsRepository,
    ): void {
        $zones = DomainZone::all();
        foreach ($zones as $zone) {
            $domainsRepository->findAllForImportDnsJob($zone->name, function ($domains) use ($dnsRepository, $zone) {
                foreach ($domains as $domain) {
                    /** @var Domain $domain */
                    try {
                        DB::beginTransaction();
                        if (\App\Models\Domain::query()->where('name', $domain->domain->name)->exists()) {
                            $this->output->warning("{$domain->domain->name} already exists. Skipped.");
                            continue;
                        }

                        $domainModel = new \App\Models\Domain();
                        $domainModel->name = $domain->domain->name;
                        $domainModel->zone_id = $zone->id;
                        $domainModel->save();

                        $dnsItems = $dnsRepository->findAllByDomain($domain)->reverse();
                        foreach ($dnsItems as $dnsItem) {
                            $dnsModel = new Dns();
                            $dnsModel->domain_id = $domainModel->id;
                            $dnsModel->date = $dnsItem->date->getTimestamp();
                            $dnsModel->hash = $dnsItem->hash;
                            $dnsModel->save();

                            foreach ($dnsItem->records as $dnsRecord) {
                                $dnsRecordModel = new DnsRecord();
                                $dnsRecordModel->dns_id = $dnsModel->id;
                                $dnsRecordModel->class = $dnsRecord->class;
                                $dnsRecordModel->type = $dnsRecord->type;
                                $dnsRecordModel->value = $dnsRecord->value;
                                $dnsRecordModel->save();
                            }
                        }
                        DB::commit();
                        $this->output->writeln($domainModel->name . ' OK');
                    } catch (\Throwable $e) {
                        $this->error($e->getMessage());
                        DB::rollBack();
                        throw $e;
                    }
                }
            });
        }
    }
}
