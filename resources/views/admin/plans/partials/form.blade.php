<form method="POST" action="{{ $action }}" class="space-y-5">
	@csrf
	@if($method === 'PUT')
		@method('PUT')
	@endif

	<x-admin.form.field label="Tên gói cước" for="name" required :error="$errors->first('name')">
		<x-admin.form.input id="name" name="name" :value="old('name', $plan?->name)" placeholder="VD: Gói Premium 30 ngày" required />
	</x-admin.form.field>

	<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
		<x-admin.form.field label="Thời hạn (ngày)" for="duration_days" required :error="$errors->first('duration_days')">
			<x-admin.form.input id="duration_days" type="number" name="duration_days" min="1" :value="old('duration_days', $plan?->duration_days)" placeholder="30" required />
		</x-admin.form.field>

		<x-admin.form.field label="Giá gói (VNĐ)" for="price" required :error="$errors->first('price')">
			<x-admin.form.input id="price" type="number" name="price" min="0" step="0.01" :value="old('price', $plan?->price)" placeholder="199000" required />
		</x-admin.form.field>
	</div>

	<x-admin.form.field label="Trạng thái kích hoạt" for="is_active" required :error="$errors->first('is_active')">
		<x-admin.form.select
			id="is_active"
			name="is_active"
			:value="(string) old('is_active', (int) ($plan?->is_active ?? 1))"
			:options="[
				['value' => '1', 'label' => 'Active — Cho phép học viên đăng ký', 'dot' => 'bg-emerald-500'],
				['value' => '0', 'label' => 'Inactive — Tạm ngưng mở bán', 'dot' => 'bg-gray-400'],
			]"
			required
		/>
	</x-admin.form.field>

	<div class="flex items-center gap-3 pt-2 border-t border-border-light">
		<button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-brand text-white text-sm font-medium rounded-lg hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
			<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
			Lưu
		</button>
		<a href="{{ route('admin.plans.index') }}" class="px-4 py-2.5 border border-border-light rounded-lg text-sm text-text-secondary hover:bg-gray-50 transition-colors cursor-pointer">Hủy</a>
	</div>
</form>
