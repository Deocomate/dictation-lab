<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportBackupRequest;
use App\Services\DataReplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class BackupController extends Controller
{
    public function __construct(
        private readonly DataReplicationService $replicationService
    ) {}

    public function index(): View
    {
        return view('admin.backups.index', [
            'stats' => $this->replicationService->getStats(),
            'recentBackups' => $this->replicationService->getRecentBackups(),
        ]);
    }

    public function export(): BinaryFileResponse
    {
        @ini_set('max_execution_time', '300');

        $filename = 'database_'.now()->format('Y-m-d_H-i-s').'.zip';
        $outputPath = config('replica.export_path').DIRECTORY_SEPARATOR.$filename;

        $filePath = $this->replicationService->export($outputPath);

        return response()->download($filePath, $filename);
    }

    public function import(ImportBackupRequest $request): RedirectResponse
    {
        @ini_set('max_execution_time', '300');

        try {
            $uploadedFile = $request->file('file');
            $originalName = $uploadedFile->getClientOriginalName();
            $skipBackup = $request->boolean('skip_backup');

            $result = $this->replicationService->import(
                $uploadedFile->getRealPath(),
                $skipBackup
            );

            $tableCount = count($result['tables']);
            $rowCount = array_sum($result['tables']);
            $imageCount = $result['images'];

            $exportTimeStr = null;
            if (! empty($result['manifest']['exported_at'])) {
                $exportTimeStr = Carbon::parse($result['manifest']['exported_at'])->format('H:i:s \n\gà\y d/m/Y');
            } else {
                $exportTimeStr = $this->replicationService->extractTimeFromFilename($originalName);
            }

            $timeInfo = $exportTimeStr ? " (xuất bản lúc {$exportTimeStr})" : '';
            $message = "Khôi phục dữ liệu thành công! Bản sao lưu{$timeInfo} đã được cập nhật: {$rowCount} bản ghi ({$tableCount} bảng) và {$imageCount} hình ảnh.";
            if ($result['backup_path']) {
                $backupName = basename($result['backup_path']);
                $message .= " Bản sao lưu dữ liệu trước khi nhập đã được lưu tại: {$backupName}.";
            }

            return back()->with('success', $message);
        } catch (Throwable $e) {
            return back()->with('error', 'Có lỗi xảy ra khi khôi phục dữ liệu: '.$e->getMessage());
        }
    }

    public function download(string $filename): BinaryFileResponse|RedirectResponse
    {
        $path = $this->replicationService->findBackupFile($filename);

        if (! $path) {
            return back()->with('error', 'Tệp sao lưu không tồn tại hoặc đường dẫn không hợp lệ.');
        }

        return response()->download($path, $filename);
    }

    public function destroy(string $filename): RedirectResponse
    {
        $deleted = $this->replicationService->deleteBackupFile($filename);

        if (! $deleted) {
            return back()->with('error', 'Không tìm thấy tệp sao lưu cần xóa.');
        }

        return back()->with('success', "Đã xóa bản sao lưu {$filename} thành công.");
    }
}
