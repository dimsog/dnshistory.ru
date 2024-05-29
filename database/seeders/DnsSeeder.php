<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Utils\DnsRecordTableResolver;
use App\Utils\DnsTableResolver;
use App\ValueObjects\Domain;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DnsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domains = [
            1 => 'dnshistory.ru',
            2 => 'dimsog.ru',
            3 => 'ness-chern.ru',
        ];

        foreach ($domains as $id => $domainName) {
            $domain = new Domain($domainName);
            for ($i = 0; $i < 30; $i++) {
                $dnsId = DB::table(DnsTableResolver::resolve($domain))
                    ->insertGetId([
                        'domain_id' => $id,
                        'date' => time(),
                        'hash' => substr(md5(time() . $domainName . $id . $i), 0, 8),
                    ]);

                // вставим подзаписи dns
                for ($j = 0; $j < 5; $j++) {
                    DB::table(DnsRecordTableResolver::resolve($domain))
                        ->insert([
                            'dns_id' => $dnsId,
                            'type' => 'TXT',
                            'class' => 'IN',
                            'value' => 'verify-testing',
                        ]);

                    DB::table(DnsRecordTableResolver::resolve($domain))
                        ->insert([
                            'dns_id' => $dnsId,
                            'type' => 'MX',
                            'class' => 'IN',
                            'value' => 'verify-testing-mx',
                        ]);

                    DB::table(DnsRecordTableResolver::resolve($domain))
                        ->insert([
                            'dns_id' => $dnsId,
                            'type' => 'A',
                            'class' => 'IN',
                            'value' => '127.0.0.1',
                        ]);
                }
            }
        }
    }
}
