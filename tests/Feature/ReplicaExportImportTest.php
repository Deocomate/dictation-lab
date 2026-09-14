<?php

use App\Services\DataReplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function replicaService(): DataReplicationService
{
    return app(DataReplicationService::class);
}

test('export bundles db rows and images into a single archive', function () {
    Storage::fake('public');

    DB::table('categories')->insert([
        'name' => 'Grammar', 'slug' => 'grammar', 'status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    Storage::disk('public')->put('articles/a.jpg', 'binary-content');

    $zipPath = sys_get_temp_dir().'/replica-export-test-'.uniqid().'.zip';

    $result = replicaService()->export($zipPath);

    expect($result)->toBe($zipPath)
        ->and(file_exists($zipPath))->toBeTrue();

    $zip = new ZipArchive;
    $zip->open($zipPath);
    expect($zip->locateName('manifest.json'))->not->toBeFalse()
        ->and($zip->locateName('db/categories.json'))->not->toBeFalse()
        ->and($zip->locateName('images/articles/a.jpg'))->not->toBeFalse();
    $zip->close();

    @unlink($zipPath);
});

test('import overwrites matching rows/files but keeps rows/files unique to the target', function () {
    Storage::fake('public');
    config(['replica.backup_path' => sys_get_temp_dir().'/replica-test-backups-'.uniqid()]);

    // Snapshot state: one row + one file that will diverge after export, plus
    // one row + one file that must survive untouched (not present in export).
    DB::table('categories')->insert([
        ['id' => 1, 'name' => 'Original Name', 'slug' => 'shared', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
    ]);
    Storage::disk('public')->put('articles/shared.jpg', 'ORIGINAL');

    $zipPath = sys_get_temp_dir().'/replica-import-test-'.uniqid().'.zip';
    replicaService()->export($zipPath);

    // Diverge locally after the export snapshot was taken.
    DB::table('categories')->where('id', 1)->update(['name' => 'Changed Locally']);
    DB::table('categories')->insert([
        ['id' => 2, 'name' => 'Local Only', 'slug' => 'local-only', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
    ]);
    Storage::disk('public')->put('articles/shared.jpg', 'LOCALLY-CHANGED');
    Storage::disk('public')->put('articles/local-only.png', 'kept-file');

    $result = replicaService()->import($zipPath, skipBackup: false);

    expect($result['backup_path'])->not->toBeNull()
        ->and(file_exists($result['backup_path']))->toBeTrue()
        ->and($result['tables']['categories'])->toBe(1)
        ->and($result['images'])->toBe(1);

    // Matching PK: imported version wins over the local edit.
    $this->assertDatabaseHas('categories', ['id' => 1, 'name' => 'Original Name']);
    // Local-only row: untouched, not deleted.
    $this->assertDatabaseHas('categories', ['id' => 2, 'name' => 'Local Only']);

    // Matching file path: imported bytes win.
    expect(Storage::disk('public')->get('articles/shared.jpg'))->toBe('ORIGINAL');
    // Local-only file: kept.
    expect(Storage::disk('public')->exists('articles/local-only.png'))->toBeTrue();

    @unlink($zipPath);
    @unlink($result['backup_path']);
});
