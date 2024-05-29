<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Repositories\DnsWorkerLogRepository;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class DnsWorkerLogRepositoryTest extends TestCase
{
    use LazilyRefreshDatabase;


    public function test_increase(): void
    {
        /** @var DnsWorkerLogRepository $repository */
        $repository = $this->app->make(DnsWorkerLogRepository::class);
        $repository->increaseNetworkError('http://bad-server');
        $repository->increaseNetworkError('http://bad-server');
        $repository->increaseNetworkError('http://bad-server');
        $repository->increaseNetworkError('http://bad-server2');

        $this->assertDatabaseHas('dns_worker_logs', [
            'date' => date('Y-m-d'),
            'server' => 'http://bad-server',
            'key' => 'network_error',
            'errors' => 3,
        ]);

        $this->assertDatabaseHas('dns_worker_logs', [
            'date' => date('Y-m-d'),
            'server' => 'http://bad-server2',
            'key' => 'network_error',
            'errors' => 1,
        ]);
    }
}
