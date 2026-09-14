<x-admin.layout.app title="AI Assistant Settings" active="ai-assistant">
	<div class="w-full max-w-full space-y-6">
		<div class="bg-white border border-border-light rounded-xl shadow-card p-5 sm:p-6">
			<h2 class="text-base font-bold text-text-primary">Cấu hình mini AI chat box</h2>
			<p class="text-sm text-text-secondary mt-1">
				Admin có thể đặt system instruction để AI luôn bám sát nguyên tắc tư vấn.
				Mỗi conversation giới hạn tối đa 5 câu hỏi để kiểm soát chi phí token.
			</p>

			<form method="POST" action="{{ route('admin.ai-assistant.update') }}" class="mt-6 space-y-5">
				@csrf
				@method('PUT')

				<div class="flex items-center justify-between gap-3 p-4 bg-app-bg border border-border-light rounded-lg">
					<div>
						<p class="text-sm font-semibold text-text-primary">Bật / tắt AI Assistant</p>
						<p class="text-xs text-text-secondary mt-1">Tắt nếu bạn không muốn hiển thị bong bóng chat với người dùng.</p>
					</div>
					<label class="inline-flex items-center gap-2 cursor-pointer">
						<input type="hidden" name="is_enabled" value="0" />
						<input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $setting->is_enabled)) class="w-4 h-4 text-brand border-border-light rounded" />
						<span class="text-sm font-medium text-text-primary">Đang bật</span>
					</label>
				</div>

				<x-admin.form.field label="Tin nhắn chào mặc định" for="welcome_message">
					<x-admin.form.input
						id="welcome_message"
						name="welcome_message"
						:value="old('welcome_message', $setting->welcome_message)"
						placeholder="Xin chào, mình có thể giúp bạn điều gì?"
					/>
				</x-admin.form.field>

				<div class="grid sm:grid-cols-2 gap-4">
					<x-admin.form.field label="Số câu hỏi tối đa / conversation" for="max_questions" hint="Bắt buộc <= 5">
						<x-admin.form.input
							id="max_questions"
							type="number"
							name="max_questions"
							min="1"
							max="5"
							:value="old('max_questions', $setting->max_questions)"
						/>
					</x-admin.form.field>

					<x-admin.form.field label="Giới hạn độ dài câu hỏi (ký tự)" for="max_input_chars" hint="Nên để 300-600">
						<x-admin.form.input
							id="max_input_chars"
							type="number"
							name="max_input_chars"
							min="100"
							max="1200"
							:value="old('max_input_chars', $setting->max_input_chars)"
						/>
					</x-admin.form.field>
				</div>

				<x-admin.form.field label="System instruction" for="system_instruction" hint="Nạp cố định trước khi AI chat">
					<x-admin.form.textarea
						id="system_instruction"
						name="system_instruction"
						rows="10"
						class="font-mono text-xs"
						:value="old('system_instruction', $setting->system_instruction)"
						placeholder="Nhập bộ hướng dẫn cố định mà AI phải tuân thủ..."
					/>
				</x-admin.form.field>

				<div class="flex items-center justify-end gap-2 pt-2 border-t border-border-light">
					<button type="submit" class="inline-flex items-center gap-1.5 bg-brand text-white rounded-xl px-5 py-2.5 text-sm font-semibold hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
						Lưu cấu hình AI
					</button>
				</div>
			</form>
		</div>
	</div>
</x-admin.layout.app>
