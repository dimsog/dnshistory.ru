<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V2;

use App\Actions\LoadWhoisAction;
use App\Converters\SiteAvailabilityToArrayConverter;
use App\Entity\SiteAvailability;
use App\Services\SiteAvailabilityLoader;
use Throwable;
use App\Converters\DnsToArrayConverter;
use App\Converters\WhoisToArrayConverter;
use App\Exceptions\DomainNameNotSupportedException;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\DomainRequest;
use App\Repositories\DnsRepository;
use App\Repositories\DomainsRepository;
use App\Repositories\DomainZonesRepository;
use App\Services\DnsLoader;
use App\Utils\Telemetry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

final class DnsController extends Controller
{
    public function __construct(
        private readonly DomainsRepository $domainsRepository,
        private readonly DnsRepository $dnsRepository,
        private readonly DnsToArrayConverter $dnsToArrayConverter,
        private readonly WhoisToArrayConverter $whoisToArrayConverter,
        private readonly DomainZonesRepository $domainZonesRepository,
        private readonly Telemetry $telemetry,
        private readonly DnsLoader $dnsLoader,
        private readonly SiteAvailabilityLoader $siteAvailabilityLoader,
        private readonly SiteAvailabilityToArrayConverter $siteAvailabilityToArrayConverter,
        private readonly LoadWhoisAction $loadWhoisAction,
    ) {
    }

    public function __invoke(DomainRequest $request): JsonResponse
    {
        $this->telemetry->send("Запрос по домену: " . $request->getDomain()->name . ' с IP: ' . $request->ip());

        try {
            if (!$this->domainZonesRepository->exists($request->getDomain()->getZone())) {
                $this->telemetry->send("Неизвестная доменная зона: " . $request->getDomain()->getZone());
                return ApiResponse::error('Доменная зона не поддерживается');
            }

            $domain = $this->domainsRepository->findOrCreate($request->getDomain());

            [$whois, $whoisErrorText] = $this->loadWhoisAction->handle($request->getDomain());

            $currentDns = $this->dnsLoader->load($domain);
            $dnsItems = $this->dnsRepository->findAllByDomain($domain);

            if ($whois == null || $currentDns == null) {
                $siteAvailability = new SiteAvailability(
                    domainId: 0,
                    date: now(),
                    status: 0,
                    latency: 0,
                );
            } else {
                $siteAvailability = $this->siteAvailabilityLoader->load($domain);
            }

            return ApiResponse::success([
                'whois' => $whois == null ? null : $this->whoisToArrayConverter->convert($whois),
                'whoisErrorText' => $whoisErrorText,

                'currentDns' => $currentDns == null ? null : $this->dnsToArrayConverter->convert($currentDns),

                'dnsHistoryItems' => $this->dnsToArrayConverter->convertAll($dnsItems),

                'siteAvailability' => $this->siteAvailabilityToArrayConverter->convert($siteAvailability),
            ]);
        } catch (DomainNameNotSupportedException $e) {
            return ApiResponse::error($e->getMessage());
        } catch (Throwable $e) {
            Log::error($e->getMessage(), [
                'exception' => $e,
            ]);
            $this->telemetry->exception($e);
            return ApiResponse::error('При загрузке данных произошла ошибка.');
        }
    }
}
