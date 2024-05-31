<?php

namespace App\Providers;

use App\Contracts\TelemetryInterface;
use App\Monitoring\Services\AdminNotification;
use App\Monitoring\Services\AdminNotificationService;
use App\Monitoring\Services\FailedJobsMonitoringService;
use App\Repositories\DnsWorkerLogRepository;
use App\Repositories\FailedJobsRepository;
use App\Logic\Telegram;
use App\Services\DnsLoader;
use App\Services\WhoisLoader;
use App\Utils\Telemetry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Iodev\Whois\Factory;
use Iodev\Whois\Loaders\CurlLoader;
use TelegramBot\Api\BotApi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BotApi::class, static function (): BotApi {
            return new BotApi(config('app.telegram.token'));
        });

        $this->app->singleton(Telegram::class, function (): Telegram {
            return new Telegram(
                $this->app->make(BotApi::class),
                config('app.telegram.users')
            );
        });

        $this->app->bind(WhoisLoader::class, static function (): WhoisLoader {
            return new WhoisLoader(
                Factory::get()->createWhois(
                    new CurlLoader(5),
                ),
            );
        });

        $this->app->bind(TelemetryInterface::class, Telemetry::class);
        $this->app->bind(AdminNotification::class, AdminNotificationService::class);
        $this->app->singleton(FailedJobsMonitoringService::class, function (): FailedJobsMonitoringService {
            return new FailedJobsMonitoringService(
                $this->app->make(AdminNotification::class),
                $this->app->make(FailedJobsRepository::class),
                (int) config('app.monitoring.failed_jobs')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Log::shareContext([
            'log.id' => time()
        ]);
    }
}
