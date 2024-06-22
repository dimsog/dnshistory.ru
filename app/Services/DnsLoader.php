<?php

declare(strict_types=1);

namespace App\Services;

use BlueLibraries\Dns\Handlers\Types\TCP;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Entity\DnsRecord;
use App\Repositories\DomainsRepository;
use App\Utils\DnsHashGenerator;
use App\Entity\Dns;
use App\Entity\Domain;
use App\ValueObjects\Domain as DomainVo;
use BlueLibraries\Dns\DnsRecords;
use BlueLibraries\Dns\Handlers\Types\Dig;
use Illuminate\Support\Collection;
use BlueLibraries\Dns\Records\Types\A;
use BlueLibraries\Dns\Records\Types\CNAME;
use BlueLibraries\Dns\Records\Types\MX;
use BlueLibraries\Dns\Records\Types\NS;
use BlueLibraries\Dns\Records\Types\TXT;
use BlueLibraries\Dns\Records\Types\AAAA;

final class DnsLoader
{
    public function __construct(
        private readonly DomainsRepository $domainsRepository,
    ) {
    }

    /**
     * Метод для пакетной загрузки dns по списку доменов
     * @param string[] $domains
     * @return array<string, Dns>
     */
    public function loadBatch(array $domains): array
    {
        $dns = [];
        foreach ($domains as $domain) {
            $domain = $this->domainsRepository->findDomain(new DomainVo($domain));
            $dns[$domain->domain->name] = $this->load($domain);
        }
        return $dns;
    }

    /**
     * @param Domain $domain
     * @return ?Dns
     */
    public function load(Domain $domain): ?Dns
    {
        try {
            $service = new DnsRecords(
                (new Dig())->setTimeout(10)
                    ->setRetries(1)
                    ->setNameserver('8.8.8.8'),
            );
            $records = $service->get($domain->domain->name, [
                1, // A
                16, // TXT
                15, // MX
                2, // NS,
                5, // CNAME
            ]);
            $records = $this->convertDnsRecords($records);
            return new Dns(
                id: 0,
                date: new \DateTimeImmutable(),
                hash: DnsHashGenerator::generate($records),
                records: $records,
            );
        } catch (Throwable $e) {
            // nothing
            return null;
        }
    }

    private function convertDnsRecords(array $records): Collection
    {
        $dnsRecords = [];
        foreach ($records as $record) {
            $value = null;
            $type = null;
            if ($record instanceof A) {
                $type = 'A';
                $value = $record->getIp();
            }
            if ($record instanceof AAAA) {
                $type = 'AAAA';
                $value = $record->getIPV6();
            }
            if ($record instanceof NS) {
                $type = 'NS';
                $value = $record->getTarget();
            }
            if ($record instanceof MX) {
                $type = 'MX';
                $value = $record->getTarget();
            }
            if ($record instanceof TXT) {
                $type = 'TXT';
                $value = $record->getTxt();
            }
            if ($record instanceof CNAME) {
                $type = 'CNAME';
                $value = $record->getTarget();
            }

            if ($type === null || $value === null) {
                continue;
            }

            $dnsRecords[] = new DnsRecord(
                dnsId: 0,
                type: mb_substr($type, 0, 6, 'UTF-8'),
                class: mb_substr($record->getClass(), 0, 6, 'UTF-8'),
                value: mb_substr($value, 0, 128, 'UTF-8'),
            );
        }
        return collect($dnsRecords);
    }
}
