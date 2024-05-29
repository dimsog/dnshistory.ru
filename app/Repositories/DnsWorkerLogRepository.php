<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\DnsWorkerLog;

final class DnsWorkerLogRepository
{
    public function increaseNetworkError(string $server): void
    {
        $model = DnsWorkerLog::firstOrCreate([
            'date' => date('Y-m-d'),
            'server' => $server,
            'key' => DnsWorkerLog::NETWORK_ERROR,
        ]);

        $model->increment('errors');
    }

    public function increaseBadResponseError(string $server): void
    {
        $model = DnsWorkerLog::firstOrCreate([
            'date' => date('Y-m-d'),
            'server' => $server,
            'key' => DnsWorkerLog::BAD_RESPONSE_ERROR,
        ]);

        $model->increment('errors');
    }
}
