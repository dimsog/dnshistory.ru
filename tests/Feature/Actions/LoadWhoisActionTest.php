<?php

declare(strict_types=1);

namespace Tests\Feature\Actions;

use App\Actions\LoadWhoisAction;
use App\Services\WhoisLoader;
use App\ValueObjects\Domain;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Modules\Tld\TldInfo;
use Iodev\Whois\Modules\Tld\TldResponse;
use Iodev\Whois\Whois;
use Tests\TestCase;

final class LoadWhoisActionTest extends TestCase
{
    public function test_load_whois_success(): void
    {
        $ioDevWhoisMockService = $this->mock(Whois::class, function ($mock) {
            $tldInfo = new TldInfo(
                response: new TldResponse([]),
                data: [
                    'creationDate' => 1704150000,
                    'expirationDate' => 1706655600,
                    'registrar' => 'Test',
                    'nameServers' => ['ns1', 'ns2'],
                    'states' => ['state1', 'state2', 'state3'],
                ],
            );
            $mock->shouldReceive('loadDomainInfo')
                ->once()
                ->andReturn($tldInfo);
        });
        $loadWhoisAction = new LoadWhoisAction(
            new WhoisLoader(
                $ioDevWhoisMockService,
            ),
        );
        /** @var \App\Entity\Whois $whois */
        [$whois, $error] = $loadWhoisAction->handle(new Domain("domain.ru"));
        $this->assertNotNull($whois);
        $this->assertNull($error);
        $this->assertSame(1704150000, $whois->createdAt->getTimestamp());
        $this->assertSame(1706655600, $whois->paidTill->getTimestamp());
        $this->assertSame('Test', $whois->registrar);
        $this->assertSame(['ns1', 'ns2'], $whois->nameServers);
        $this->assertSame(['state1', 'state2', 'state3'], $whois->states);
    }

    public function test_load_whois_fail_because_data_is_empty(): void
    {
        $ioDevWhoisMockService = $this->mock(Whois::class, function ($mock) {
            $mock->shouldReceive('loadDomainInfo')
                ->once()
                ->andReturn(null);
        });
        $loadWhoisAction = new LoadWhoisAction(
            new WhoisLoader(
                $ioDevWhoisMockService,
            ),
        );
        [$whois, $error] = $loadWhoisAction->handle(new Domain("domain.ru"));
        $this->assertNull($whois);
        $this->assertSame("Не удалось загрузить информацию по домену", $error);
    }

    public function test_load_whois_fail_because_connection_exception(): void
    {
        $ioDevWhoisMockService = $this->mock(Whois::class, function ($mock) {
            $mock->shouldReceive('loadDomainInfo')
                ->andThrow(new ConnectionException())
                ->once();
        });
        $loadWhoisAction = new LoadWhoisAction(
            new WhoisLoader(
                $ioDevWhoisMockService,
            ),
        );
        [$whois, $error] = $loadWhoisAction->handle(new Domain("domain.ru"));
        $this->assertNull($whois);
    }

    public function test_load_whois_success_but_expiration_date_is_empty(): void
    {
        $ioDevWhoisMockService = $this->mock(Whois::class, function ($mock) {
            $tldInfo = new TldInfo(
                response: new TldResponse([]),
                data: [
                    'creationDate' => 1704150000,
                    'expirationDate' => 0,
                    'registrar' => 'Test',
                    'nameServers' => ['ns1', 'ns2'],
                    'states' => ['state1', 'state2', 'state3'],
                ],
            );
            $mock->shouldReceive('loadDomainInfo')
                ->once()
                ->andReturn($tldInfo);
        });
        $loadWhoisAction = new LoadWhoisAction(
            new WhoisLoader(
                $ioDevWhoisMockService,
            ),
        );
        /** @var \App\Entity\Whois $whois */
        [$whois, $error] = $loadWhoisAction->handle(new Domain("domain.ru"));
        $this->assertNull($whois->paidTill);
    }
}
