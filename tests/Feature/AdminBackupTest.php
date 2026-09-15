<?php

use App\Models\User;
use App\Services\DataReplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('guests and normal users cannot access backup management', function () {
    $this->get(route('admin.backups.index'))
        ->assertRedirect(route('admin.auth.login'));

    $client = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
    ]);

    $this->actingAs($client)
        ->get(route('admin.backups.index'))
        ->assertForbidden();

    $regularAdmin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);

    $this->actingAs($regularAdmin)
        ->get(route('admin.backups.index'))
        ->assertForbidden();
});

test('superadmin can access backup management and see stats', function () {
    $superadmin = User::factory()->create([
        'role' => 'superadmin',
        'status' => 'active',
    ]);

    $response = $this->actingAs($superadmin)
        ->get(route('admin.backups.index'));

    $response->assertOk()
        ->assertSee('Sao lưu & Phục hồi dữ liệu')
        ->assertSee('Xuất dữ liệu hệ thống (Export)')
        ->assertSee('Khôi phục dữ liệu (Import)')
        ->assertSee('Tải về tệp sao lưu (.zip)');
});

test('superadmin can export database and images with timestamped zip filename', function () {
    Storage::fake('public');
    Storage::disk('public')->put('articles/sample.jpg', 'fake-image-content');

    $superadmin = User::factory()->create([
        'role' => 'superadmin',
        'status' => 'active',
    ]);

    $response = $this->actingAs($superadmin)
        ->post(route('admin.backups.export'));

    $response->assertOk();
    expect($response->headers->get('Content-Disposition'))
        ->toMatch('/attachment; filename=database_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.zip/');
});

test('data replication service can extract timestamp from filename and archive', function () {
    $service = app(DataReplicationService::class);

    $timeStr = $service->extractTimeFromFilename('database_2026-09-15_13-47-30.zip');
    expect($timeStr)->toBe('13:47:30 ngày 15/09/2026');

    $timeStr2 = $service->extractTimeFromFilename('replica-20260915-134730.zip');
    expect($timeStr2)->toBe('13:47:30 ngày 15/09/2026');
});

test('superadmin can import valid replica zip file and see export time in message', function () {
    Storage::fake('public');

    $superadmin = User::factory()->create([
        'role' => 'superadmin',
        'status' => 'active',
    ]);

    DB::table('categories')->insert([
        'name' => 'Original Category',
        'slug' => 'original-category',
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    Storage::disk('public')->put('articles/imported.jpg', 'ORIGINAL_IMAGE');

    $service = app(DataReplicationService::class);
    $tempZip = sys_get_temp_dir().'/database_2026-09-15_13-47-30.zip';
    $service->export($tempZip);

    // Change category locally to test overwrite
    DB::table('categories')->where('slug', 'original-category')->update(['name' => 'Locally Modified']);

    $uploadedFile = new UploadedFile(
        $tempZip,
        'database_2026-09-15_13-47-30.zip',
        'application/zip',
        null,
        true
    );

    $response = $this->actingAs($superadmin)
        ->from(route('admin.backups.index'))
        ->post(route('admin.backups.import'), [
            'file' => $uploadedFile,
            'skip_backup' => false,
        ]);

    $response->assertRedirect(route('admin.backups.index'))
        ->assertSessionHas('success');

    $flashMessage = session('success');
    expect($flashMessage)->toContain('xuất bản lúc');

    // Overwritten by zip content
    $this->assertDatabaseHas('categories', [
        'slug' => 'original-category',
        'name' => 'Original Category',
    ]);

    if (File::exists($tempZip)) {
        File::delete($tempZip);
    }
});

test('import validation fails if file is missing or not a zip', function () {
    $superadmin = User::factory()->create([
        'role' => 'superadmin',
        'status' => 'active',
    ]);

    $response = $this->actingAs($superadmin)
        ->from(route('admin.backups.index'))
        ->post(route('admin.backups.import'), []);

    $response->assertRedirect(route('admin.backups.index'))
        ->assertSessionHasErrors(['file']);

    $txtFile = UploadedFile::fake()->create('test.txt', 10, 'text/plain');

    $response = $this->actingAs($superadmin)
        ->from(route('admin.backups.index'))
        ->post(route('admin.backups.import'), [
            'file' => $txtFile,
        ]);

    $response->assertRedirect(route('admin.backups.index'))
        ->assertSessionHasErrors(['file']);
});
