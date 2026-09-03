@php use App\Helpers\FormatHelper; @endphp

<x-admin.layout.app title="Tổng quan hệ thống" active="dashboard">
	@php
		$charts = $charts ?? [];
		$transactionValues = $charts['transaction_status']['values'] ?? [0, 0, 0];
	@endphp

	{{-- Stats Cards --}}
	<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
		<div class="bg-white rounded-xl border border-border-light p-4 shadow-card">
			<div class="flex items-center justify-between mb-3">
				<p class="text-xs font-medium text-text-secondary">Tổng học viên</p>
				<div class="w-8 h-8 bg-brand-light rounded-lg flex items-center justify-center">
					<svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
				</div>
			</div>
			<p class="text-2xl font-extrabold text-text-primary">{{ number_format($summary['total_users']) }}</p>
			<p class="text-xs text-brand mt-1 font-medium">+{{ $summary['new_users_week'] }} trong 7 ngày</p>
		</div>
		<div class="bg-white rounded-xl border border-border-light p-4 shadow-card">
			<div class="flex items-center justify-between mb-3">
				<p class="text-xs font-medium text-text-secondary">Học viên Pro</p>
				<div class="w-8 h-8 bg-yellow-50 rounded-lg flex items-center justify-center">
					<svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
				</div>
			</div>
			<p class="text-2xl font-extrabold text-text-primary">{{ number_format($summary['pro_users']) }}</p>
			<p class="text-xs text-brand mt-1 font-medium">+{{ $summary['new_pro_week'] }} trong 7 ngày</p>
		</div>
		<div class="bg-white rounded-xl border border-border-light p-4 shadow-card">
			<div class="flex items-center justify-between mb-3">
				<p class="text-xs font-medium text-text-secondary">Doanh thu tháng</p>
				<div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
					<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
				</div>
			</div>
			<p class="text-2xl font-extrabold text-text-primary">{{ FormatHelper::money($summary['monthly_revenue']) }}</p>
		</div>
		<div class="bg-white rounded-xl border border-border-light p-4 shadow-card">
			<div class="flex items-center justify-between mb-3">
				<p class="text-xs font-medium text-text-secondary">Tổng bài viết</p>
				<div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
					<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
				</div>
			</div>
			<p class="text-2xl font-extrabold text-text-primary">{{ number_format($summary['total_articles']) }}</p>
		</div>
	</div>

	{{-- Analytics Charts --}}
	<div class="grid xl:grid-cols-2 gap-6 mb-6">
		<div class="bg-white rounded-xl border border-border-light shadow-card p-5">
			<div class="flex items-center justify-between mb-4">
				<h2 class="text-sm font-semibold text-text-primary">Tăng trưởng học viên</h2>
				<span class="text-xs text-text-secondary">{{ $charts['users_growth']['window_days'] ?? 14 }} ngày</span>
			</div>
			<div class="h-72">
				<canvas id="adminUsersGrowthChart"></canvas>
			</div>
			<p id="adminUsersGrowthEmpty" class="hidden mt-2 text-xs text-text-secondary">Chưa có dữ liệu tăng trưởng học viên trong kỳ.</p>
		</div>

		<div class="bg-white rounded-xl border border-border-light shadow-card p-5">
			<div class="flex items-center justify-between mb-4">
				<h2 class="text-sm font-semibold text-text-primary">Doanh thu theo tháng</h2>
				<span class="text-xs text-text-secondary">{{ $charts['revenue_trend']['window_months'] ?? 6 }} tháng</span>
			</div>
			<div class="h-72">
				<canvas id="adminRevenueTrendChart"></canvas>
			</div>
			<p id="adminRevenueTrendEmpty" class="hidden mt-2 text-xs text-text-secondary">Chưa có dữ liệu doanh thu thành công trong kỳ.</p>
		</div>
	</div>

	<div class="grid xl:grid-cols-3 gap-6 mb-6">
		<div class="xl:col-span-2 bg-white rounded-xl border border-border-light shadow-card p-5">
			<div class="flex items-center justify-between mb-4">
				<h2 class="text-sm font-semibold text-text-primary">Hoạt động luyện tập toàn hệ thống</h2>
				<span class="text-xs text-text-secondary">{{ $charts['learning_activity']['window_days'] ?? 14 }} ngày</span>
			</div>
			<div class="h-72">
				<canvas id="adminLearningActivityChart"></canvas>
			</div>
			<p id="adminLearningActivityEmpty" class="hidden mt-2 text-xs text-text-secondary">Chưa có dữ liệu hoạt động học tập trong kỳ.</p>
		</div>

		<div class="bg-white rounded-xl border border-border-light shadow-card p-5">
			<div class="flex items-center justify-between mb-4">
				<h2 class="text-sm font-semibold text-text-primary">Trạng thái giao dịch</h2>
				<span class="text-xs text-text-secondary">{{ $charts['transaction_status']['window_days'] ?? 30 }} ngày</span>
			</div>
			<div class="h-52">
				<canvas id="adminTransactionStatusChart"></canvas>
			</div>
			<ul class="mt-4 space-y-2 text-xs text-text-secondary">
				<li class="flex items-center justify-between"><span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Thành công</span><strong class="text-text-primary">{{ number_format((int) ($transactionValues[0] ?? 0)) }}</strong></li>
				<li class="flex items-center justify-between"><span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-500"></span>Chờ xác nhận</span><strong class="text-text-primary">{{ number_format((int) ($transactionValues[1] ?? 0)) }}</strong></li>
				<li class="flex items-center justify-between"><span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-500"></span>Thất bại</span><strong class="text-text-primary">{{ number_format((int) ($transactionValues[2] ?? 0)) }}</strong></li>
			</ul>
			<p id="adminTransactionStatusEmpty" class="hidden mt-2 text-xs text-text-secondary">Chưa có dữ liệu giao dịch trong kỳ.</p>
		</div>
	</div>

	{{-- Recent data --}}
	<div class="grid lg:grid-cols-2 gap-6">
		{{-- Recent users --}}
		<div class="bg-white rounded-xl border border-border-light shadow-card">
			<div class="px-5 py-4 border-b border-border-light flex items-center justify-between">
				<h2 class="text-sm font-semibold text-text-primary">Học viên mới nhất</h2>
				<a href="{{ route('admin.clients.index') }}" class="text-xs text-brand hover:text-brand-dark font-medium cursor-pointer transition-colors">Xem tất cả</a>
			</div>
			<div class="divide-y divide-border-light">
				@forelse($recentUsers as $user)
					<div class="px-5 py-3 flex items-center gap-3">
						@php $initial = strtoupper(substr($user->name, 0, 1)); $colors = ['A'=>'purple','B'=>'blue','C'=>'orange','D'=>'green','E'=>'pink','F'=>'indigo','G'=>'yellow','H'=>'red','K'=>'teal','L'=>'cyan','M'=>'emerald','N'=>'violet','O'=>'fuchsia','P'=>'rose','Q'=>'amber','R'=>'lime','S'=>'sky','T'=>'blue','U'=>'purple','V'=>'orange','X'=>'green']; $color = $colors[$initial] ?? 'blue'; @endphp
						<div class="w-8 h-8 rounded-full bg-{{ $color }}-100 flex items-center justify-center text-xs font-bold text-{{ $color }}-700 shrink-0">{{ $initial }}</div>
						<div class="flex-1 min-w-0">
							<p class="text-sm font-medium text-text-primary truncate">{{ $user->name }}</p>
							<p class="text-xs text-text-secondary truncate">{{ $user->email }}</p>
						</div>
						@if($user->subscription_tier === 'pro')
							<span class="px-2 py-0.5 bg-brand-light text-brand text-xs font-semibold rounded-md">Pro</span>
						@else
							<span class="px-2 py-0.5 bg-gray-100 text-text-secondary text-xs rounded-md">Free</span>
						@endif
					</div>
				@empty
					<p class="px-5 py-6 text-sm text-text-secondary text-center">Chưa có dữ liệu.</p>
				@endforelse
			</div>
		</div>

		{{-- Recent transactions --}}
		<div class="bg-white rounded-xl border border-border-light shadow-card">
			<div class="px-5 py-4 border-b border-border-light flex items-center justify-between">
				<h2 class="text-sm font-semibold text-text-primary">Giao dịch gần đây</h2>
				<a href="{{ route('admin.transactions.index') }}" class="text-xs text-brand hover:text-brand-dark font-medium cursor-pointer transition-colors">Xem tất cả</a>
			</div>
			<div class="divide-y divide-border-light">
				@forelse($recentTransactions as $transaction)
					<div class="px-5 py-3 flex items-center justify-between gap-3">
						<div class="min-w-0">
							<p class="text-sm font-medium text-text-primary truncate">{{ $transaction->user?->name ?? 'N/A' }}</p>
							<p class="text-xs text-text-secondary">{{ $transaction->payment_method ?? 'N/A' }} &middot; {{ FormatHelper::dateTime($transaction->created_at, 'd/m/Y') }}</p>
						</div>
						<div class="text-right shrink-0">
							<p class="text-sm font-bold text-brand">{{ FormatHelper::money($transaction->amount) }}</p>
							@if($transaction->status === 'success')
								<span class="text-[11px] px-1.5 py-0.5 bg-green-100 text-green-700 font-medium rounded">Thành công</span>
							@elseif($transaction->status === 'pending')
								<span class="text-[11px] px-1.5 py-0.5 bg-yellow-100 text-yellow-700 font-medium rounded">Chờ xác nhận</span>
							@else
								<span class="text-[11px] px-1.5 py-0.5 bg-red-100 text-red-700 font-medium rounded">Thất bại</span>
							@endif
						</div>
					</div>
				@empty
					<p class="px-5 py-6 text-sm text-text-secondary text-center">Chưa có dữ liệu.</p>
				@endforelse
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
	<script>
		const adminCharts = @json($charts);

		function hasDataset(dataset) {
			return Array.isArray(dataset) && dataset.some((value) => value !== null && value !== undefined && Number(value) > 0);
		}

		function renderUsersGrowthChart() {
			const target = document.getElementById('adminUsersGrowthChart');
			const emptyMessage = document.getElementById('adminUsersGrowthEmpty');
			const labels = adminCharts?.users_growth?.labels ?? [];
			const totalUsers = adminCharts?.users_growth?.total ?? [];
			const proUsers = adminCharts?.users_growth?.pro ?? [];

			if (!target || !labels.length || (!hasDataset(totalUsers) && !hasDataset(proUsers))) {
				if (emptyMessage) {
					emptyMessage.classList.remove('hidden');
				}
				return;
			}

			new Chart(target, {
				type: 'line',
				data: {
					labels,
					datasets: [
						{ label: 'Học viên mới', data: totalUsers, borderColor: '#0EA5E9', backgroundColor: 'rgba(14,165,233,0.15)', tension: 0.35, fill: true },
						{ label: 'Học viên Pro mới', data: proUsers, borderColor: '#11A683', backgroundColor: 'rgba(17,166,131,0.12)', tension: 0.35, fill: false },
					],
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: { legend: { position: 'bottom' } },
					scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
				},
			});
		}

		function renderRevenueTrendChart() {
			const target = document.getElementById('adminRevenueTrendChart');
			const emptyMessage = document.getElementById('adminRevenueTrendEmpty');
			const labels = adminCharts?.revenue_trend?.labels ?? [];
			const revenue = adminCharts?.revenue_trend?.revenue ?? [];

			if (!target || !labels.length || !hasDataset(revenue)) {
				if (emptyMessage) {
					emptyMessage.classList.remove('hidden');
				}
				return;
			}

			new Chart(target, {
				type: 'bar',
				data: {
					labels,
					datasets: [
						{ label: 'Doanh thu (VND)', data: revenue, backgroundColor: 'rgba(17,166,131,0.72)', borderRadius: 8 },
					],
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: { position: 'bottom' },
						tooltip: {
							callbacks: {
								label(context) {
									const value = Number(context.parsed.y || 0);
									return `Doanh thu: ${value.toLocaleString('vi-VN')} VND`;
								},
							},
						},
					},
					scales: {
						y: {
							beginAtZero: true,
							ticks: {
								callback(value) {
									return `${Number(value).toLocaleString('vi-VN')}đ`;
								},
							},
						},
					},
				},
			});
		}

		function renderLearningActivityChart() {
			const target = document.getElementById('adminLearningActivityChart');
			const emptyMessage = document.getElementById('adminLearningActivityEmpty');
			const labels = adminCharts?.learning_activity?.labels ?? [];
			const dictation = adminCharts?.learning_activity?.dictation ?? [];

			if (!target || !labels.length || !hasDataset(dictation)) {
				if (emptyMessage) {
					emptyMessage.classList.remove('hidden');
				}
				return;
			}

			new Chart(target, {
				type: 'bar',
				data: {
					labels,
					datasets: [
						{ label: 'Chép chính tả', data: dictation, backgroundColor: 'rgba(17,166,131,0.75)' },
					],
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: { legend: { position: 'bottom' } },
					scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
				},
			});
		}

		function renderTransactionStatusChart() {
			const target = document.getElementById('adminTransactionStatusChart');
			const emptyMessage = document.getElementById('adminTransactionStatusEmpty');
			const labels = adminCharts?.transaction_status?.labels ?? [];
			const values = adminCharts?.transaction_status?.values ?? [];

			if (!target || !labels.length || !hasDataset(values)) {
				if (emptyMessage) {
					emptyMessage.classList.remove('hidden');
				}
				return;
			}

			new Chart(target, {
				type: 'doughnut',
				data: {
					labels,
					datasets: [
						{ data: values, backgroundColor: ['#16A34A', '#F59E0B', '#EF4444'], borderWidth: 0 },
					],
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: { legend: { position: 'bottom' } },
				},
			});
		}

		renderUsersGrowthChart();
		renderRevenueTrendChart();
		renderLearningActivityChart();
		renderTransactionStatusChart();
	</script>
</x-admin.layout.app>
