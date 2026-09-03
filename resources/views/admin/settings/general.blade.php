<x-admin.layout.app title="Cấu hình chung" active="general-settings">
	<div class="w-full max-w-full space-y-6">
		<div class="bg-white border border-border-light rounded-xl shadow-card p-5 sm:p-6">
			<h2 class="text-base font-bold text-text-primary">Video demo trang chủ</h2>
			<p class="text-sm text-text-secondary mt-1">
				Dán link YouTube ở dạng watch, youtu.be, embed hoặc shorts. Hệ thống sẽ tự lưu thành URL embed chuẩn.
			</p>

			<form method="POST" action="{{ route('admin.settings.general.update') }}" class="mt-6 space-y-5">
				@csrf
				@method('PUT')

				<div>
					<label for="demo_video_url" class="block text-sm font-semibold text-text-primary mb-1.5">URL YouTube demo</label>
					<input
						id="demo_video_url"
						type="url"
						name="demo_video_url"
						value="{{ old('demo_video_url', $demoVideoUrl) }}"
						class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm"
						placeholder="https://www.youtube.com/watch?v=..."
					/>
					<p class="text-xs text-text-secondary mt-1">Để trống nếu chưa muốn hiển thị popup video trên trang chủ.</p>
				</div>

				@if($demoVideoUrl)
					<div class="rounded-xl border border-border-light overflow-hidden bg-black">
						<div class="aspect-video">
							<iframe src="{{ $demoVideoUrl }}" class="w-full h-full border-0" title="Video demo hiện tại" allowfullscreen></iframe>
						</div>
					</div>
				@endif

				<div class="flex justify-end">
					<button type="submit" class="inline-flex items-center gap-1.5 bg-brand text-white rounded-lg px-4 py-2.5 text-sm font-semibold hover:bg-brand-dark transition-colors cursor-pointer">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
						Lưu cấu hình
					</button>
				</div>
			</form>
		</div>
	</div>
</x-admin.layout.app>
