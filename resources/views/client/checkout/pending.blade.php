@php use App\Helpers\FormatHelper; @endphp
<x-client.layout.app title="Đang chờ thanh toán">
    <div class="max-w-xl mx-auto py-12 px-4 text-center">
        <div class="bg-white rounded-2xl border border-border-light shadow-card p-8 text-center relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute top-0 left-0 w-full h-2 bg-yellow-400"></div>

            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            
            <h2 class="text-xl font-bold mb-2 text-text-primary">Đang chờ thanh toán...</h2>
            <p class="text-text-secondary mb-6 text-sm">Hệ thống đang chờ xác nhận từ ngân hàng. Vui lòng không đóng trình duyệt.</p>
            
            @if(!empty($qrImageUrl))
                <div class="mb-6 inline-block bg-white p-3 border border-border-light rounded-xl">
                    <img src="{{ $qrImageUrl }}" class="mx-auto w-48 h-48" alt="QR Code" />
                </div>
            @endif

            <div class="bg-app-bg rounded-xl p-5 mb-6 text-left border border-border-light space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-text-secondary">Mã đơn hàng:</span>
                    <span class="font-mono text-text-primary font-medium">{{ $transaction->gateway_order_code ?? $transaction->transaction_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-secondary">Số tiền:</span>
                    <span class="text-brand font-bold">{{ FormatHelper::money($transaction->amount) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-secondary">Trạng thái:</span>
                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded">Chờ thanh toán</span>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 text-brand mb-6">
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="font-medium text-sm">Đang tự động kiểm tra...</span>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                @if(!empty($transaction->checkout_url))
                    <a href="{{ $transaction->checkout_url }}" target="_blank" class="w-full sm:w-auto px-6 py-3 bg-brand text-white font-medium rounded-lg hover:bg-brand-dark transition-colors text-center cursor-pointer">
                        Thanh toán ngay
                    </a>
                @endif
                <a href="{{ route('client.dashboard') }}" class="w-full sm:w-auto px-6 py-3 bg-white text-text-secondary border border-border-light font-medium rounded-lg hover:bg-app-bg transition-colors text-center cursor-pointer">
                    Về Dashboard
                </a>
            </div>
            
            <p class="text-xs text-text-disabled mt-5">Trang sẽ tự động cập nhật khi thanh toán thành công.</p>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            // Tự động kiểm tra trạng thái giao dịch mỗi 3 giây
            setInterval(() => {
                fetch("{{ route('client.checkout.pending', $transaction->id) }}", {
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                .then(response => {
                    // Nếu server trả về chuyển hướng (redirect) -> Trình duyệt sẽ tự động chạy theo
                    if (response.redirected) {
                        window.location.href = response.url;
                    }
                });
            }, 3000);
        </script>
    </x-slot:scripts>
</x-client.layout.app>
