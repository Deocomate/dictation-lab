<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AiAssistantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiAssistantController extends Controller
{
    public function __construct(private readonly AiAssistantService $aiAssistantService) {}

    public function edit(): View
    {
        return view('admin.settings.ai-assistant', [
            'setting' => $this->aiAssistantService->getSetting(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'is_enabled' => ['nullable', 'boolean'],
            'system_instruction' => ['required', 'string', 'min:20', 'max:20000'],
            'max_questions' => ['required', 'integer', 'between:1,5'],
            'max_input_chars' => ['required', 'integer', 'between:100,1200'],
            'welcome_message' => ['nullable', 'string', 'max:255'],
        ]);

        $data['is_enabled'] = $request->boolean('is_enabled');

        $this->aiAssistantService->updateSetting($data, (int) auth()->id());

        return back()->with('success', 'Đã cập nhật cấu hình AI Assistant thành công.');
    }
}
