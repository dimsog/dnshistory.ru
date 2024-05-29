<?php

declare(strict_types=1);

namespace Tests\Feature\Commands;

use App\Contracts\TelemetryInterface;
use App\Jobs\DnsJob;
use App\Repositories\DomainsRepository;
use App\Testing\TelemetryMock;
use App\ValueObjects\Domain;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class SendDomainsToImportDnsCommandTest extends TestCase
{
    use LazilyRefreshDatabase;


    public function test_send_domains_to_import(): void
    {
        Queue::fake();
        $this->app->bind(TelemetryInterface::class, TelemetryMock::class);

        /** @var DomainsRepository $repository */
        $repository = $this->app->make(DomainsRepository::class);
        $repository->add(new Domain('domain.ru'));
        $repository->add(new Domain('domain.com'));
        $this->artisan('app:dns-import')->assertSuccessful();

        Queue::assertPushedOn('dns', DnsJob::class);
        Queue::assertPushed(DnsJob::class, 2);
    }
}
