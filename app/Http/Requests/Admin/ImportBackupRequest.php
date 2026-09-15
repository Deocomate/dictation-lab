<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportBackupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:zip', 'max:512000'],
            'skip_backup' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Vui lòng chọn hoặc kéo thả file zip dữ liệu cần khôi phục.',
            'file.file' => 'Tệp tải lên không hợp lệ.',
            'file.mimes' => 'Hệ thống chỉ chấp nhận tệp nén định dạng .zip.',
            'file.max' => 'Dung lượng tệp tải lên không được vượt quá 500MB.',
        ];
    }
}
