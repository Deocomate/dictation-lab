@php use App\Helpers\FormatHelper; @endphp

<x-admin.layout.app title="Chi tiết học viên" active="clients">
    @php
        $periodDays = $filters['period_days'] ?? 30;
        $perPage = $filters['per_page'] ?? 15;

        $avgWpm = $summary['avg_wpm'] !== null ? number_format($summary['avg_wpm'], 0) : '--';
        $avgAccuracy = $summary['avg_accuracy'] !== null ? number_format($summary['avg_accuracy'], 2) : '--';
    @endphp

    <div class="mb-4 flex items-center gap-2 text-sm">
        <a href="{{ route('admin.clients.index') }}" class="text-text-secondary hover:text-brand transition-colors cursor-pointer">Học viên</a>
        <svg class="w-3.5 h-3.5 text-text-disabled" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-text-primary font-medium">{{ $client->name }}</span>
    </div>

    <div x-data="{ showSubscriptionForm: {{ $errors->has('subscription_tier') || $errors->has('subscription_expires_at') ? 'true' : 'false' }} }">
        {{-- Profile card --}}
        <div class="bg-white border border-border-light rounded-xl p-5 sm:p-6 mb-6 shadow-card">
            <div class="flex flex-wrap justify-between gap-4">
                <div class="flex items-center gap-4">
                    @php $initial = strtoupper(substr($client->name, 0, 1)); @endphp
                    <div class="w-14 h-14 rounded-full {{ $client->subscription_tier === 'pro' ? 'bg-brand' : 'bg-slate-200' }} flex items-center justify-center text-xl font-bold {{ $client->subscription_tier === 'pro' ? 'text-white' : 'text-slate-600' }} shrink-0">{{ $initial }}</div>
                    <div>
                        <p class="text-xl font-bold text-text-primary">{{ $client->name }}</p>
                        <p class="text-sm text-text-secondary">{{ $client->email }}</p>
                        <div class="mt-1.5 flex items-center gap-2 flex-wrap">
                            @if($client->subscription_tier === 'pro')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-700">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                                    Pro
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-text-secondary">Free</span>
                            @endif

                            @if($client->status === 'active')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Locked
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-text-secondary mt-1">
                            @if($client->subscription_tier === 'pro' && $client->subscription_expires_at)
                                Hết hạn gói Pro: {{ FormatHelper::dateTime($client->subscription_expires_at, 'd/m/Y') }}
                            @else
                                Chưa có thời hạn gói Pro.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex flex-col gap-2 items-stretch sm:items-end">
                    <button type="button" @click="showSubscriptionForm = !showSubscriptionForm" class="inline-flex items-center justify-center gap-1.5 border border-border-light text-text-secondary rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-gray-50 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                        Chỉnh sửa gói cước
                    </button>

                    <form method="POST" action="{{ route('admin.clients.update-status', $client) }}" class="flex items-end gap-2">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="period_days" value="{{ $periodDays }}" />
                        <input type="hidden" name="per_page" value="{{ $perPage }}" />
                        <input type="hidden" name="page" value="{{ $attempts->currentPage() }}" />

                        <select name="status" class="input-admin border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all">
                            <option value="active" @selected($client->status === 'active')>Active</option>
                            <option value="locked" @selected($client->status === 'locked')>Locked</option>
                        </select>
                        <button type="submit" class="inline-flex items-center gap-1.5 bg-brand text-white rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Cập nhật trạng thái
                        </button>
                    </form>
                </div>
            </div>

            <div x-show="showSubscriptionForm" x-cloak class="mt-4 pt-4 border-t border-border-light">
                <form method="POST" action="{{ route('admin.clients.update-subscription', $client) }}" class="grid sm:grid-cols-3 gap-3 items-end">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="period_days" value="{{ $periodDays }}" />
                    <input type="hidden" name="per_page" value="{{ $perPage }}" />
                    <input type="hidden" name="page" value="{{ $attempts->currentPage() }}" />

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Subscription Tier</label>
                        <select name="subscription_tier" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all">
                            <option value="free" @selected(old('subscription_tier', $client->subscription_tier) === 'free')>Free</option>
                            <option value="pro" @selected(old('subscription_tier', $client->subscription_tier) === 'pro')>Pro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Subscription Expires At</label>
                        <input type="date" name="subscription_expires_at" value="{{ old('subscription_expires_at', optional($client->subscription_expires_at)->format('Y-m-d')) }}" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" />
                    </div>
                    <div>
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 bg-brand text-white rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Lưu gói cước
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Period filter --}}
        <div class="bg-white border border-border-light rounded-xl p-4 sm:p-5 mb-6 shadow-card">
            <form method="GET" action="{{ route('admin.clients.show', $client) }}" class="grid md:grid-cols-3 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Khung thời gian</label>
                    <select name="period_days" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all">
                        <option value="7" @selected($periodDays == 7)>7 ngày</option>
                        <option value="30" @selected($periodDays == 30)>30 ngày</option>
                        <option value="90" @selected($periodDays == 90)>90 ngày</option>
                        <option value="180" @selected($periodDays == 180)>180 ngày</option>
                        <option value="365" @selected($periodDays == 365)>365 ngày</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Số dòng mỗi trang</label>
                    <select name="per_page" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all">
                        <option value="10" @selected($perPage == 10)>10</option>
                        <option value="15" @selected($perPage == 15)>15</option>
                        <option value="20" @selected($perPage == 20)>20</option>
                        <option value="50" @selected($perPage == 50)>50</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 bg-brand text-white rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-brand-dark transition-colors cursor-pointer shadow-sm w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L14 14v6l-4-2v-4L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Lọc dữ liệu
                    </button>
                    <a href="{{ route('admin.clients.show', $client) }}" class="inline-flex items-center justify-center border border-border-light text-text-secondary rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-gray-50 transition-colors cursor-pointer">Reset</a>
                </div>
            </form>
        </div>

        {{-- Summary stats — dictation only --}}
        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-border-light p-4 shadow-card">
                <p class="text-xs font-medium text-text-secondary">Tổng lượt Dictation</p>
                <p class="text-2xl font-extrabold text-text-primary mt-2">{{ number_format($summary['total_dictations']) }}</p>
                <p class="text-xs text-text-disabled mt-1">Trong kỳ đã chọn</p>
            </div>
            <div class="bg-white rounded-xl border border-border-light p-4 shadow-card">
                <p class="text-xs font-medium text-text-secondary">WPM trung bình</p>
                <p class="text-2xl font-extrabold text-blue-600 mt-2">{{ $avgWpm }}</p>
                <p class="text-xs text-text-disabled mt-1">Tốc độ gõ chữ/phút</p>
            </div>
            <div class="bg-white rounded-xl border border-border-light p-4 shadow-card">
                <p class="text-xs font-medium text-text-secondary">Accuracy trung bình</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-2">{{ $avgAccuracy }}%</p>
                <p class="text-xs text-text-disabled mt-1">Độ chính xác chép chính tả</p>
            </div>
            <div class="bg-white rounded-xl border border-border-light p-4 shadow-card">
                <p class="text-xs font-medium text-text-secondary">Lần học gần nhất</p>
                <p class="text-lg font-bold text-text-primary mt-2">{{ FormatHelper::dateTime($summary['latest_activity_at']) ?? '--' }}</p>
                <p class="text-xs text-text-disabled mt-1">Theo kỳ đã chọn</p>
            </div>
        </div>

        {{-- Dictation trend chart --}}
        <div class="grid xl:grid-cols-2 gap-6 mb-6">
            <div class="bg-white border border-border-light rounded-xl p-4 shadow-card">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-text-primary">Xu hướng Dictation</h2>
                    <span class="text-xs text-text-disabled">WPM & Accuracy (12 lần gần nhất)</span>
                </div>
                <div class="h-64">
                    <canvas id="dictationTrendChart"></canvas>
                </div>
                <p id="dictationTrendEmpty" class="hidden text-xs text-text-secondary mt-2">Chưa có dữ liệu Dictation trong kỳ này.</p>
            </div>

            <div class="bg-white border border-border-light rounded-xl p-4 shadow-card">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-text-primary">Lượt luyện tập theo ngày</h2>
                    <span class="text-xs text-text-disabled">{{ $charts['attempt_volume']['window_days'] ?? 14 }} ngày gần nhất</span>
                </div>
                <div class="h-64">
                    <canvas id="attemptVolumeChart"></canvas>
                </div>
                <p id="attemptVolumeEmpty" class="hidden text-xs text-text-secondary mt-2">Chưa có dữ liệu luyện tập theo ngày.</p>
            </div>
        </div>

        {{-- Attempts table --}}
        <div class="bg-white border border-border-light rounded-xl overflow-hidden shadow-card">
            <div class="px-5 py-4 border-b border-border-light flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-text-primary">Chi tiết lượt Dictation</h2>
                    <p class="text-xs text-text-secondary mt-0.5">Xem từng lượt chép chính tả: bài viết, tốc độ WPM, độ chính xác.</p>
                </div>
                <span class="text-xs text-text-disabled">{{ number_format($attempts->total()) }} bản ghi</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border-light text-sm">
                    <thead class="bg-app-bg">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-text-secondary">Thời điểm</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-text-secondary">Bài viết</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-text-secondary">Danh mục</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-text-secondary">WPM</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-text-secondary">Accuracy</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-text-secondary">Thời lượng</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-light">
                        @forelse($attempts as $attempt)
                            <tr>
                                <td class="px-4 py-3 align-top">
                                    <p class="font-medium text-text-primary">{{ $attempt['done_at_label'] }}</p>
                                    <p class="text-xs text-text-secondary">ID #{{ $attempt['attempt_id'] }}</p>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <p class="font-medium text-text-primary">{{ $attempt['article_title'] }}</p>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    @if($attempt['category_name'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-brand-light text-brand">{{ $attempt['category_name'] }}</span>
                                    @else
                                        <span class="text-text-disabled text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <p class="font-medium text-blue-700">{{ number_format((int) ($attempt['wpm'] ?? 0)) }}</p>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <p class="font-medium text-emerald-700">{{ number_format((float) ($attempt['accuracy'] ?? 0), 2) }}%</p>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <p class="text-text-secondary">{{ $attempt['duration_label'] ?? 'N/A' }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-text-secondary">
                                    Không có dữ liệu Dictation trong kỳ này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($attempts->hasPages())
                <div class="px-4 py-3 border-t border-border-light bg-white">
                    {{ $attempts->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const chartPayload = @json($charts);

        function hasDataset(dataset) {
            return Array.isArray(dataset) && dataset.some(v => v !== null && v !== undefined);
        }

        (function renderDictationTrend() {
            const target = document.getElementById('dictationTrendChart');
            const empty = document.getElementById('dictationTrendEmpty');
            const labels = chartPayload?.dictation_trend?.labels ?? [];
            const datasets = chartPayload?.dictation_trend?.datasets ?? {};

            if (!target || !labels.length || !hasDataset(datasets.wpm || [])) {
                empty?.classList.remove('hidden');
                return;
            }

            new Chart(target, {
                data: {
                    labels,
                    datasets: [
                        { type: 'line', label: 'WPM', data: datasets.wpm, borderColor: '#2563EB', backgroundColor: 'rgba(37,99,235,0.14)', yAxisID: 'y', tension: 0.35 },
                        { type: 'line', label: 'Accuracy (%)', data: datasets.accuracy, borderColor: '#16A34A', backgroundColor: 'rgba(22,163,74,0.14)', yAxisID: 'y1', tension: 0.35 },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { type: 'linear', position: 'left', beginAtZero: true },
                        y1: { type: 'linear', position: 'right', beginAtZero: true, max: 100, grid: { drawOnChartArea: false } },
                    },
                },
            });
        })();

        (function renderAttemptVolume() {
            const target = document.getElementById('attemptVolumeChart');
            const empty = document.getElementById('attemptVolumeEmpty');
            const labels = chartPayload?.attempt_volume?.labels ?? [];
            const volumes = chartPayload?.attempt_volume?.dictation ?? [];

            if (!target || !labels.length || !hasDataset(volumes)) {
                empty?.classList.remove('hidden');
                return;
            }

            new Chart(target, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        { label: 'Lượt Dictation', data: volumes, backgroundColor: 'rgba(37,99,235,0.68)' },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    },
                },
            });
        })();
    </script>
</x-admin.layout.app>
