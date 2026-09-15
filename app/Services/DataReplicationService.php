<?php

namespace App\Services;

use App\Helpers\FormatHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

/**
 * Exports/imports DB rows + local images as a single zip so local and the
 * Coolify server can be replicated quickly. Import merges: rows are upserted
 * by primary key (imported version wins on conflict) and images are copied
 * over by relative path (imported file wins on conflict); anything that only
 * exists on the target side (local-only rows/files) is left untouched.
 */
class DataReplicationService
{
    private const MANIFEST_VERSION = 1;

    /**
     * Build a replica archive at $outputPath (or an auto-named one under
     * config('replica.export_path') when null) and return its path.
     */
    public function export(?string $outputPath = null): string
    {
        $workDir = $this->makeTempDir('export');

        try {
            File::ensureDirectoryExists($workDir.'/db');
            File::ensureDirectoryExists($workDir.'/images');

            $tableCounts = $this->dumpTables($workDir.'/db');
            $imageCount = $this->copyDirectoryContents($this->imagesRoot(), $workDir.'/images');

            File::put($workDir.'/manifest.json', json_encode([
                'version' => self::MANIFEST_VERSION,
                'exported_at' => now()->toIso8601String(),
                'app_env' => config('app.env'),
                'tables' => $tableCounts,
                'image_count' => $imageCount,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            $outputPath ??= config('replica.export_path').'/database_'.now()->format('Y-m-d_H-i-s').'.zip';
            File::ensureDirectoryExists(dirname($outputPath));

            $this->zipDirectory($workDir, $outputPath);

            return $outputPath;
        } finally {
            File::deleteDirectory($workDir);
        }
    }

    /**
     * Merge a replica archive (or an already-extracted directory) into the
     * current database + images disk. Returns a summary of what changed.
     *
     * @return array{backup_path: ?string, tables: array<string,int>, images: int, manifest: ?array<string,mixed>}
     */
    public function import(string $archivePath, bool $skipBackup = false): array
    {
        if (! File::exists($archivePath)) {
            throw new RuntimeException("Replica archive not found: {$archivePath}");
        }

        $backupPath = null;
        if (! $skipBackup) {
            File::ensureDirectoryExists(config('replica.backup_path'));
            $backupPath = $this->export(
                config('replica.backup_path').'/pre-import-'.now()->format('Y-m-d_H-i-s').'.zip'
            );
        }

        $extractedDir = is_dir($archivePath) ? $archivePath : $this->extractZip($archivePath);
        $isTempExtraction = $extractedDir !== $archivePath;

        try {
            $manifestPath = $extractedDir.'/manifest.json';
            $manifest = File::exists($manifestPath) ? json_decode(File::get($manifestPath), true) : null;

            $tableCounts = $this->importTables($extractedDir.'/db');
            $imageCount = $this->copyDirectoryContents($extractedDir.'/images', $this->imagesRoot());

            return [
                'backup_path' => $backupPath,
                'tables' => $tableCounts,
                'images' => $imageCount,
                'manifest' => $manifest,
            ];
        } finally {
            if ($isTempExtraction) {
                File::deleteDirectory($extractedDir);
            }
        }
    }

    /**
     * @return array{table_count: int, image_count: int}
     */
    public function getStats(): array
    {
        $excluded = config('replica.excluded_tables', []);
        $tables = array_filter(
            Schema::getTables(),
            fn ($t) => ! in_array($t['name'], $excluded, true)
        );

        $imageCount = 0;
        $imagesRoot = $this->imagesRoot();
        if (is_dir($imagesRoot)) {
            $imageCount = count(File::allFiles($imagesRoot));
        }

        return [
            'table_count' => count($tables),
            'image_count' => $imageCount,
        ];
    }

    /**
     * @return array<int, array{filename: string, path: string, size: int, size_formatted: string, created_at: int, type: string}>
     */
    public function getRecentBackups(): array
    {
        $directories = [
            'backup' => config('replica.backup_path'),
            'export' => config('replica.export_path'),
        ];

        $files = [];

        foreach ($directories as $type => $dir) {
            if (! is_dir($dir)) {
                continue;
            }

            foreach (File::files($dir) as $file) {
                if ($file->getExtension() !== 'zip') {
                    continue;
                }

                $size = $file->getSize();
                $modified = $file->getMTime();

                $files[] = [
                    'filename' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'size' => $size,
                    'size_formatted' => FormatHelper::fileSize($size),
                    'created_at' => $modified,
                    'type' => $type,
                ];
            }
        }

        usort($files, fn ($a, $b) => $b['created_at'] <=> $a['created_at']);

        return array_slice($files, 0, 20);
    }

    public function findBackupFile(string $filename): ?string
    {
        if (basename($filename) !== $filename || ! str_ends_with(strtolower($filename), '.zip')) {
            return null;
        }

        $candidates = [
            config('replica.backup_path').DIRECTORY_SEPARATOR.$filename,
            config('replica.export_path').DIRECTORY_SEPARATOR.$filename,
        ];

        foreach ($candidates as $candidate) {
            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    public function deleteBackupFile(string $filename): bool
    {
        $path = $this->findBackupFile($filename);
        if (! $path) {
            return false;
        }

        return File::delete($path);
    }

    public function extractTimeFromFilename(string $filename): ?string
    {
        // Pattern 1: YYYY-MM-DD_H-i-s or YYYY-MM-DD_His
        if (preg_match('/(\d{4})-(\d{2})-(\d{2})[_T-](\d{2})[-:]?(\d{2})[-:]?(\d{2})?/', $filename, $m)) {
            $sec = $m[6] ?? '00';

            return "{$m[4]}:{$m[5]}:{$sec} ngày {$m[3]}/{$m[2]}/{$m[1]}";
        }

        // Pattern 2: YYYYMMDD-HHmmss or YYYYMMDD_HHmmss
        if (preg_match('/(\d{4})(\d{2})(\d{2})[-_](\d{2})(\d{2})(\d{2})/', $filename, $m)) {
            return "{$m[4]}:{$m[5]}:{$m[6]} ngày {$m[3]}/{$m[2]}/{$m[1]}";
        }

        // Pattern 3: DD-MM-YYYY_H-i-s
        if (preg_match('/(\d{2})-(\d{2})-(\d{4})[_T-](\d{2})[-:]?(\d{2})[-:]?(\d{2})?/', $filename, $m)) {
            $sec = $m[6] ?? '00';

            return "{$m[4]}:{$m[5]}:{$sec} ngày {$m[1]}/{$m[2]}/{$m[3]}";
        }

        return null;
    }

    /**
     * Inspect a zip file to determine its export timestamp without full extraction.
     */
    public function getArchiveExportTime(string $archivePath): ?string
    {
        if (! File::exists($archivePath)) {
            return null;
        }

        $zip = new ZipArchive;
        if ($zip->open($archivePath) === true) {
            $manifestJson = $zip->getFromName('manifest.json');
            $zip->close();

            if ($manifestJson) {
                $data = json_decode($manifestJson, true);
                if (! empty($data['exported_at'])) {
                    return Carbon::parse($data['exported_at'])->format('H:i:s \n\gà\y d/m/Y');
                }
            }
        }

        return $this->extractTimeFromFilename(basename($archivePath));
    }

    /**
     * @return array<string,int> table name => exported row count
     */
    private function dumpTables(string $dbDir): array
    {
        $excluded = config('replica.excluded_tables', []);
        $counts = [];

        foreach (Schema::getTables() as $table) {
            $name = $table['name'];
            if (in_array($name, $excluded, true)) {
                continue;
            }

            $rows = DB::table($name)->get()->map(fn ($row) => (array) $row)->all();
            File::put($dbDir.'/'.$name.'.json', json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $counts[$name] = count($rows);
        }

        return $counts;
    }

    /**
     * @return array<string,int> table name => imported (upserted) row count
     */
    private function importTables(string $dbDir): array
    {
        if (! is_dir($dbDir)) {
            return [];
        }

        $excluded = config('replica.excluded_tables', []);
        $chunkSize = max(1, (int) config('replica.chunk_size', 50));
        $counts = [];

        Schema::disableForeignKeyConstraints();

        try {
            DB::transaction(function () use ($dbDir, $excluded, $chunkSize, &$counts) {
                foreach (File::glob($dbDir.'/*.json') as $file) {
                    $table = pathinfo($file, PATHINFO_FILENAME);

                    if (in_array($table, $excluded, true) || ! Schema::hasTable($table)) {
                        continue;
                    }

                    $rows = json_decode(File::get($file), true) ?: [];
                    if ($rows === []) {
                        continue;
                    }

                    $this->upsertRows($table, $rows, $chunkSize);
                    $counts[$table] = count($rows);
                }
            });
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        return $counts;
    }

    /**
     * @param  array<int,array<string,mixed>>  $rows
     */
    private function upsertRows(string $table, array $rows, int $chunkSize): void
    {
        $primaryKey = $this->primaryKeyColumns($table);
        $allColumns = array_keys($rows[0]);
        $updateColumns = array_values(array_diff($allColumns, $primaryKey));

        foreach (array_chunk($rows, $chunkSize) as $chunk) {
            if ($primaryKey === [] || $updateColumns === []) {
                // No usable key to upsert on (or every column is part of the
                // key, e.g. a plain pivot table) — insert new rows, silently
                // skip ones that already exist.
                DB::table($table)->insertOrIgnore($chunk);

                continue;
            }

            DB::table($table)->upsert($chunk, $primaryKey, $updateColumns);
        }
    }

    /**
     * @return array<int,string>
     */
    private function primaryKeyColumns(string $table): array
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (! empty($index['primary'])) {
                return $index['columns'];
            }
        }

        return [];
    }

    private function imagesRoot(): string
    {
        return Storage::disk(config('replica.images_disk', 'public'))->path('');
    }

    /**
     * Copy every file from $source into $destination, overwriting files that
     * already exist at the same relative path and leaving everything else in
     * $destination untouched. Returns the number of files copied.
     */
    private function copyDirectoryContents(string $source, string $destination): int
    {
        if (! is_dir($source)) {
            return 0;
        }

        File::ensureDirectoryExists($destination);
        $copied = 0;

        /** @var \SplFileInfo $file */
        foreach (File::allFiles($source) as $file) {
            $relative = $file->getRelativePathname();
            $target = $destination.DIRECTORY_SEPARATOR.$relative;

            File::ensureDirectoryExists(dirname($target));
            File::copy($file->getPathname(), $target);
            $copied++;
        }

        return $copied;
    }

    private function zipDirectory(string $sourceDir, string $outputPath): void
    {
        if (File::exists($outputPath)) {
            File::delete($outputPath);
        }

        $zip = new ZipArchive;
        if ($zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException("Unable to create archive: {$outputPath}");
        }

        /** @var \SplFileInfo $file */
        foreach (File::allFiles($sourceDir) as $file) {
            // Zip entry names must use "/" regardless of host OS, so archives
            // built on Windows extract correctly on the Linux Coolify server.
            $entryName = str_replace('\\', '/', $file->getRelativePathname());
            $zip->addFile($file->getPathname(), $entryName);
        }

        $zip->close();
    }

    private function extractZip(string $zipPath): string
    {
        $target = $this->makeTempDir('import');

        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException("Unable to open archive: {$zipPath}");
        }

        $zip->extractTo($target);
        $zip->close();

        return $target;
    }

    private function makeTempDir(string $prefix): string
    {
        $dir = storage_path('app/private/replica-tmp/'.$prefix.'-'.uniqid());
        File::ensureDirectoryExists($dir);

        return $dir;
    }
}
