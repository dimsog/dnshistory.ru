<?php

declare(strict_types=1);

namespace App\Monitoring\Services;

use App\Repositories\Repository;

final class FailedJobsMonitoringService
{
    public function __construct(
        private readonly AdminNotification $adminNotificationService,
        private readonly Repository $failedJobsRepository,
        private readonly int $limit
    ) {
    }

    public function execute(): void
    {
        $totalFailedJobs = $this->failedJobsRepository->count();
        if ($totalFailedJobs > $this->limit) {
            $this->adminNotificationService
                ->notify('Внимание! В failed_jobs: ' . $totalFailedJobs . ' ошибок!');
        }
    }
}
