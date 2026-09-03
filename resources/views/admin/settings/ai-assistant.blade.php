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

				<div>
					<label for="welcome_message" class="block text-sm font-semibold text-text-primary mb-1.5">Tin nhắn chào mặc định</label>
					<input
						id="welcome_message"
						type="text"
						name="welcome_message"
						value="{{ old('welcome_message', $setting->welcome_message) }}"
						class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm"
						placeholder="Xin chào, mình có thể giúp bạn điều gì?"
					/>
				</div>

				<div class="grid sm:grid-cols-2 gap-4">
					<div>
						<label for="max_questions" class="block text-sm font-semibold text-text-primary mb-1.5">Số câu hỏi tối đa / conversation</label>
						<input
							id="max_questions"
							type="number"
							name="max_questions"
							min="1"
							max="5"
							value="{{ old('max_questions', $setting->max_questions) }}"
							class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm"
						/>
						<p class="text-xs text-text-secondary mt-1">Bắt buộc <= 5 để tránh vượt chi phí token.</p>
					</div>
					<div>
						<label for="max_input_chars" class="block text-sm font-semibold text-text-primary mb-1.5">Giới hạn độ dài câu hỏi (ký tự)</label>
						<input
							id="max_input_chars"
							type="number"
							name="max_input_chars"
							min="100"
							max="1200"
							value="{{ old('max_input_chars', $setting->max_input_chars) }}"
							class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm"
						/>
						<p class="text-xs text-text-secondary mt-1">Nên để 300-600 để tối ưu token mỗi lần hỏi.</p>
					</div>
				</div>

				<div>
					<label for="system_instruction" class="block text-sm font-semibold text-text-primary mb-1.5">System instruction</label>
					<textarea
						id="system_instruction"
						name="system_instruction"
						rows="12"
						class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm leading-6 font-mono"
						placeholder="Nhập bộ hướng dẫn cố định mà AI phải tuân thủ..."
					>{{ old('system_instruction', $setting->system_instruction) }}</textarea>
					<p class="text-xs text-text-secondary mt-1">Tất cả câu trả lời AI cho user sẽ luôn nạp instruction này trước khi chat.</p>
				</div>

				<div class="flex items-center justify-end gap-2">
					<button type="submit" class="inline-flex items-center gap-1.5 bg-brand text-white rounded-lg px-4 py-2.5 text-sm font-semibold hover:bg-brand-dark transition-colors cursor-pointer">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
						Lưu cấu hình AI
					</button>
				</div>
			</form>
		</div>
	</div>
</x-admin.layout.app>
