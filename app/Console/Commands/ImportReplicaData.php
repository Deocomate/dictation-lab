<?php

namespace App\Console\Commands;

use App\Services\DataReplicationService;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class ImportReplicaData extends Command
{
    use ConfirmableTrait;

    protected $signature = 'replica:import
        {path : Path to a replica zip archive, or an already-extracted replica directory}
        {--skip-backup : Skip the automatic pre-import backup of current data}
        {--force : Skip the production confirmation prompt}';

    protected $description = 'Import a replica archive, merging it into the current DB + images (overwrites matching rows/files, keeps local-only ones)';

    public function handle(DataReplicationService $service): int
    {
        if (! $this->confirmToProceed(
            'This will overwrite any local rows/files that also exist in the archive.'
        )) {
            return self::FAILURE;
        }

        $result = $service->import($this->argument('path'), (bool) $this->option('skip-backup'));

        if ($result['backup_path']) {
            $this->info("Pre-import backup written to: {$result['backup_path']}");
        }

        $this->info('Tables merged:');
        foreach ($result['tables'] as $table => $count) {
            $this->line("  - {$table}: {$count} row(s)");
        }

        $this->info("Images merged: {$result['images']}");

        return self::SUCCESS;
    }
}
