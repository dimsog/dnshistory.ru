<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

final class FailedJobsRepository implements Repository
{
    public function count(): int
    {
        return DB::table('failed_jobs')->count();
    }
}
