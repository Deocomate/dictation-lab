<x-client.layout.app title="Thanh toán gói cước">
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-text-primary">Nâng cấp tài khoản Pro</h1>
        <p class="text-text-secondary mt-2">Mở khóa toàn bộ tính năng và bài thi thử chấm điểm bằng AI</p>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-8">
        {{-- Chọn Gói --}}
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-text-primary mb-3">1. Chọn gói cước</h3>
            <form id="checkout-form" method="POST" action="{{ route('client.checkout.process') }}">
                @csrf
                <div class="space-y-3">
                    @php
                        $selectedPlanId = old('plan_id', $plan?->id) ?? $plans->first()?->id;
                    @endphp
                    @foreach($plans as $p)
                    <label class="relative flex flex-col p-5 border-2 cursor-pointer rounded-2xl transition-all duration-200 hover:bg-gray-50
                        {{ ($selectedPlanId == $p->id) ? 'border-brand bg-brand-light/30' : 'border-border-light bg-white' }}">
                        
                        <input type="radio" name="plan_id" value="{{ $p->id }}" class="sr-only" {{ ($selectedPlanId == $p->id) ? 'checked' : '' }} onchange="updateSelectedPlan(this)">
                        
                        <div class="flex justify-between items-center w-full">
                            <span class="text-lg font-bold text-text-primary">{{ $p->name }}</span>
                            <span class="text-xl font-extrabold text-brand">{{ \App\Helpers\FormatHelper::money($p->price) }}</span>
                        </div>
                        <p class="text-sm text-text-secondary mt-1">Sử dụng trong {{ $p->duration_days }} ngày</p>
                    </label>
                    @endforeach
                </div>
            </form>
        </div>

        {{-- Thanh Toán --}}
        <div>
            <h3 class="text-lg font-semibold text-text-primary mb-3">2. Tổng thanh toán</h3>
            <div class="bg-white border border-border-light rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-6 p-4 bg-blue-50 text-blue-700 rounded-xl">
                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm font-medium">Bạn sẽ được chuyển hướng an toàn đến Cổng thanh toán SePay. Hỗ trợ quét mã QR mọi ngân hàng.</p>
                </div>

                <div class="border-t border-border-light pt-4 mb-6">
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span>Tổng cộng:</span>
                        <span class="text-2xl text-brand" id="total-display">--</span>
                    </div>
                </div>

                <button type="button" onclick="document.getElementById('checkout-form').submit()" class="w-full py-3.5 bg-brand hover:bg-brand-dark text-white text-base font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Thanh toán bảo mật
                </button>
                <div class="mt-4 flex justify-center items-center gap-2">
                    <span class="text-xs text-text-disabled">Powered by</span>
                    <img src="https://sepay.vn/assets/img/logo-sepay.png" alt="SePay" class="h-4 grayscale opacity-60">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateSelectedPlan(input) {
        document.querySelectorAll('input[name="plan_id"]').forEach(el => {
            const label = el.closest('label');
            if (el.checked) {
                label.classList.remove('border-border-light', 'bg-white');
                label.classList.add('border-brand', 'bg-brand-light/30');
            } else {
                label.classList.remove('border-brand', 'bg-brand-light/30');
                label.classList.add('border-border-light', 'bg-white');
            }
        });

        const plans = @json($plans->keyBy('id'));
        if (input && plans[input.value]) {
            const price = Number(plans[input.value].price);
            document.getElementById('total-display').innerText = new Intl.NumberFormat('vi-VN').format(price) + 'đ';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const checked = document.querySelector('input[name="plan_id"]:checked');
        if (checked) {
            updateSelectedPlan(checked);
        }
    });
</script>
</x-client.layout.app>
