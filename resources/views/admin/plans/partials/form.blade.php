<form method="POST" action="{{ $action }}" class="space-y-5">
	@csrf
	@if($method === 'PUT')
		@method('PUT')
	@endif

	<div>
		<label for="name" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Tên gói</label>
		<input id="name" name="name" value="{{ old('name', $plan?->name) }}" placeholder="VD: Gói Premium" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required />
	</div>
	<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
		<div>
			<label for="duration_days" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Thời hạn (ngày)</label>
			<input id="duration_days" type="number" min="1" name="duration_days" value="{{ old('duration_days', $plan?->duration_days) }}" placeholder="30" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required />
		</div>
		<div>
			<label for="price" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Giá (VNĐ)</label>
			<input id="price" type="number" min="0" step="0.01" name="price" value="{{ old('price', $plan?->price) }}" placeholder="199000" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required />
		</div>
	</div>
	<div>
		<label for="is_active" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Trạng thái</label>
		<select id="is_active" name="is_active" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required>
			<option value="1" @selected((string) old('is_active', (int) ($plan?->is_active ?? 1)) === '1')>Active</option>
			<option value="0" @selected((string) old('is_active', (int) ($plan?->is_active ?? 1)) === '0')>Inactive</option>
		</select>
	</div>

	<div class="flex items-center gap-3 pt-2 border-t border-border-light">
		<button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-brand text-white text-sm font-medium rounded-lg hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
			<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
			Lưu
		</button>
		<a href="{{ route('admin.plans.index') }}" class="px-4 py-2.5 border border-border-light rounded-lg text-sm text-text-secondary hover:bg-gray-50 transition-colors cursor-pointer">Hủy</a>
	</div>
</form>
