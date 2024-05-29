<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DomainsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ru_domains')->insertOrIgnore([
            [
                'id' => 1,
                'name' => 'dnshistory.ru',
            ],
            [
                'id' => 2,
                'name' => 'dimsog.ru',
            ],
            [
                'id' => 3,
                'name' => 'ness-chern.ru',
            ],
        ]);
    }
}
