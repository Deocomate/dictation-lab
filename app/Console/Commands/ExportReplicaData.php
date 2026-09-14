<?php

namespace App\Console\Commands;

use App\Services\DataReplicationService;
use Illuminate\Console\Command;

class ExportReplicaData extends Command
{
    protected $signature = 'replica:export {--output= : Destination zip path (defaults under storage/app/private/replica-exports)}';

    protected $description = 'Export DB data + local images into a single replica archive';

    public function handle(DataReplicationService $service): int
    {
        $path = $service->export($this->option('output'));

        $this->info("Replica archive written to: {$path}");

        return self::SUCCESS;
    }
}
