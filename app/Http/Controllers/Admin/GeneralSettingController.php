<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateGeneralSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GeneralSettingController extends Controller
{
    public function __construct(private readonly SettingService $settingService) {}

    public function edit(): View
    {
        return view('admin.settings.general', [
            'demoVideoUrl' => $this->settingService->getDemoVideoUrl(),
        ]);
    }

    public function update(UpdateGeneralSettingRequest $request): RedirectResponse
    {
        $this->settingService->updateDemoVideoUrl($request->validated('demo_video_url'));

        return back()->with('success', 'Đã cập nhật cấu hình chung thành công.');
    }
}
