@php use App\Helpers\FormatHelper; @endphp

<x-admin.layout.app title="Quản lý giao dịch" active="transactions">
	{{-- Header --}}
	<div class="mb-5">
		<p class="text-sm text-text-secondary">Theo dõi và cập nhật trạng thái giao dịch thanh toán.</p>
	</div>

	{{-- Table --}}
	<div class="bg-white border border-border-light rounded-xl overflow-hidden shadow-card">
		<div class="overflow-x-auto">
			<table class="min-w-full text-sm table-hover">
				<thead>
					<tr class="bg-gray-50/80 border-b border-border-light">
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Mã GD</th>
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Học viên</th>
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Gói</th>
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Số tiền</th>
						<th class="px-5 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">Trạng thái</th>
						<th class="px-5 py-3 text-right text-xs font-semibold text-text-secondary uppercase tracking-wider">Thao tác</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-border-light">
					@forelse($transactions as $transaction)
						<tr>
							<td class="px-5 py-3.5">
								<span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded text-text-primary">{{ $transaction->transaction_code ?? 'N/A' }}</span>
							</td>
							<td class="px-5 py-3.5">
								<div class="flex items-center gap-2.5">
									@php $initial = strtoupper(substr($transaction->user?->name ?? '?', 0, 1)); @endphp
									<div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600 flex-shrink-0">{{ $initial }}</div>
									<span class="text-text-primary">{{ $transaction->user?->name ?? 'N/A' }}</span>
								</div>
							</td>
							<td class="px-5 py-3.5 text-text-secondary">{{ $transaction->plan?->name ?? 'N/A' }}</td>
							<td class="px-5 py-3.5 font-semibold text-text-primary">{{ FormatHelper::money($transaction->amount) }}</td>
							<td class="px-5 py-3.5">
								@if($transaction->status === 'success')
									<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700">
										<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
										Success
									</span>
								@elseif($transaction->status === 'pending')
									<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-700">
										<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
										Pending
									</span>
								@else
									<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-600">
										<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
										Failed
									</span>
								@endif
							</td>
							<td class="px-5 py-3.5">
								<div class="flex justify-end gap-1.5">
									<form method="POST" action="{{ route('admin.transactions.update-status', $transaction) }}" class="flex gap-1.5">
										@csrf
										@method('PUT')
										<select name="status" class="input-admin border border-border-light rounded-lg px-2.5 py-1.5 text-xs transition-all">
											<option value="pending" @selected($transaction->status === 'pending')>Pending</option>
											<option value="success" @selected($transaction->status === 'success')>Success</option>
											<option value="failed" @selected($transaction->status === 'failed')>Failed</option>
										</select>
										<button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-brand text-white text-xs font-medium hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
											<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
											Lưu
										</button>
									</form>
									<form method="POST" action="{{ route('admin.transactions.destroy', $transaction) }}" onsubmit="return confirm('Bạn có chắc muốn xóa giao dịch này?')">
										@csrf
										@method('DELETE')
										<button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium text-red-600 hover:bg-red-50 border border-border-light transition-all cursor-pointer">
											<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
											Xóa
										</button>
									</form>
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6" class="px-5 py-12 text-center">
								<p class="text-sm text-text-secondary">Chưa có giao dịch nào.</p>
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>

	<div class="mt-4">{{ $transactions->links() }}</div>
</x-admin.layout.app>
