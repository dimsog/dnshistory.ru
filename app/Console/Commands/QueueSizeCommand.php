<?php

namespace App\Console\Commands;

use App\Models\DomainZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class QueueSizeCommand extends Command
{
    protected $signature = 'app:queue-size';

    protected $description = 'Размер очереди';


    public function handle(): void
    {
        $zones = DomainZone::all();
        foreach ($zones as $zone) {
            $queue = $zone->name . '_dns';
            $size = Queue::size($queue);
            if ($size > 0) {
                $this->output->writeln("{$zone->name}: " . $size);
            }
        }
    }
}
