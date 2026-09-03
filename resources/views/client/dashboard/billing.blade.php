<x-client.layout.dashboard title="Gói cước" activePage="billing">
  <x-slot:headerContent>
    <div>
      <h1 class="text-lg font-bold text-text-primary">Gói cước & Thanh toán</h1>
      <p class="text-xs text-text-secondary">Quản lý gói cước và lịch sử thanh toán.</p>
    </div>
  </x-slot:headerContent>

  <div class="w-full space-y-6">
    {{-- Current plan --}}
    <div class="bg-white rounded-xl border border-border-light p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-text-primary">Gói hiện tại</h2>
        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $user->isPro() ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-text-secondary' }}">
          {{ $user->isPro() ? 'Pro' : 'Free' }}
        </span>
      </div>

      @if($user->isPro())
        <p class="text-sm text-text-secondary">Gói Pro của bạn có hiệu lực đến <strong class="text-text-primary">{{ $user->subscription_expires_at->format('d/m/Y') }}</strong>.</p>
      @else
        <p class="text-sm text-text-secondary mb-4">Bạn đang dùng gói Free. Nâng cấp để mở khóa toàn bộ tính năng.</p>
        <div class="bg-gradient-to-r from-brand to-brand-dark rounded-xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="text-white">
            <p class="font-bold text-sm flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              Nâng cấp Pro ngay
            </p>
            <p class="text-xs opacity-80 mt-1">Chép không giới hạn, 1000+ bài song ngữ, AI dịch từ vựng.</p>
          </div>
          <a href="{{ route('client.checkout') }}" class="flex-shrink-0 px-4 py-2 bg-white text-brand text-sm font-bold rounded-lg hover:bg-brand-light transition-colors">Xem gói cước →</a>
        </div>
      @endif
    </div>

    {{-- Feature comparison table --}}
    <div class="bg-white rounded-xl border border-border-light p-6">
      <h2 class="font-semibold text-text-primary mb-4">So sánh tính năng</h2>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-border-light">
              <th class="text-left py-3 text-text-secondary font-medium">Tính năng</th>
              <th class="text-center py-3 text-text-secondary font-medium w-24">Free</th>
              <th class="text-center py-3 text-text-secondary font-medium w-24">Pro</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr>
              <td class="py-3 text-text-primary">Chép chính tả song ngữ</td>
              <td class="py-3 text-center text-text-secondary">3 bài/ngày</td>
              <td class="py-3 text-center text-brand font-semibold">Không giới hạn</td>
            </tr>
            <tr>
              <td class="py-3 text-text-primary">Thư viện bài Premium</td>
              <td class="py-3 text-center">
                <svg class="w-4 h-4 text-text-disabled mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </td>
              <td class="py-3 text-center">
                <svg class="w-4 h-4 text-brand mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              </td>
            </tr>
            <tr>
              <td class="py-3 text-text-primary">AI dịch từ vựng</td>
              <td class="py-3 text-center">
                <svg class="w-4 h-4 text-text-disabled mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </td>
              <td class="py-3 text-center">
                <svg class="w-4 h-4 text-brand mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              </td>
            </tr>
            <tr>
              <td class="py-3 text-text-primary">AI giải thích ngữ pháp câu</td>
              <td class="py-3 text-center">
                <svg class="w-4 h-4 text-text-disabled mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </td>
              <td class="py-3 text-center">
                <svg class="w-4 h-4 text-brand mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              </td>
            </tr>
            <tr>
              <td class="py-3 text-text-primary">Sổ tay từ vựng</td>
              <td class="py-3 text-center text-text-secondary">Tối đa 50 từ</td>
              <td class="py-3 text-center">
                <svg class="w-4 h-4 text-brand mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    {{-- Transaction history --}}
    <div class="bg-white rounded-xl border border-border-light p-6">
      <h2 class="font-semibold text-text-primary mb-4">Lịch sử giao dịch</h2>
      @if($transactions->isEmpty())
        <div class="text-center py-6">
          <p class="text-sm text-text-disabled">Chưa có giao dịch nào.</p>
        </div>
      @else
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-border-light">
                <th class="text-left py-3 text-text-secondary font-medium">Ngày</th>
                <th class="text-left py-3 text-text-secondary font-medium">Gói</th>
                <th class="text-left py-3 text-text-secondary font-medium">Số tiền</th>
                <th class="text-left py-3 text-text-secondary font-medium">Trạng thái</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
              @foreach($transactions as $txn)
                <tr>
                  <td class="py-3 text-text-primary">{{ $txn->created_at->format('d/m/Y') }}</td>
                  <td class="py-3 text-text-primary">{{ $txn->plan->name ?? '-' }}</td>
                  <td class="py-3 text-text-primary">{{ number_format($txn->amount) }}đ</td>
                  <td class="py-3">
                    @php
                      $statusColors = [
                        'completed' => 'bg-green-100 text-brand',
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'failed' => 'bg-red-100 text-semantic-red',
                      ];
                      $statusLabels = ['completed' => 'Thành công', 'pending' => 'Đang chờ', 'failed' => 'Thất bại'];
                    @endphp
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusColors[$txn->status] ?? 'bg-gray-100 text-text-secondary' }}">
                      {{ $statusLabels[$txn->status] ?? $txn->status }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-4">{{ $transactions->links() }}</div>
      @endif
    </div>
  </div>
</x-client.layout.dashboard>
