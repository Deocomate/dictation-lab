@php use App\Helpers\FormatHelper; @endphp
<x-client.layout.app title="Thanh toán thành công">
    <x-slot:head>
        <style>
            @keyframes confetti-fall {
                0% { transform: translateY(-20px) rotate(0deg); opacity: 1; }
                100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
            }
            .confetti {
                position: fixed;
                top: 0;
                pointer-events: none;
                animation: confetti-fall linear forwards;
                z-index: 9999;
            }
        </style>
    </x-slot:head>

    <div id="confettiContainer"></div>

    <div class="max-w-2xl mx-auto py-12 px-4 sm:px-6">
        <div class="bg-white rounded-2xl border border-border-light shadow-card p-8 text-center overflow-hidden relative">
            <!-- Background Decoration -->
            <div class="absolute top-0 left-0 w-full h-2 bg-brand"></div>
            
            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 relative">
                <svg class="w-10 h-10 text-brand" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <div class="absolute inset-0 rounded-full border-4 border-brand/30 animate-ping"></div>
            </div>
            
            <h1 class="text-2xl font-bold text-text-primary mb-2">Thanh toán thành công!</h1>
            <p class="text-text-secondary mb-8">Chào mừng bạn đến với <strong class="text-brand">Dictation Lab Pro</strong>. Tài khoản của bạn đã được nâng cấp.</p>

            <div class="bg-app-bg rounded-xl p-5 mb-8 text-left border border-border-light">
                <h3 class="text-sm font-semibold text-text-primary uppercase tracking-wide border-b border-border-light pb-3 mb-3">Thông tin giao dịch</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-text-secondary">Mã giao dịch:</span>
                        <span class="font-mono text-text-primary font-medium">#{{ $transaction->transaction_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text-secondary">Gói cước:</span>
                        <span class="text-text-primary font-medium">{{ $transaction->plan->name ?? 'Pro' }} — {{ $transaction->plan->duration_days ?? 30 }} ngày</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text-secondary">Số tiền:</span>
                        <span class="text-brand font-bold">{{ FormatHelper::money($transaction->amount) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text-secondary">Trạng thái tài khoản:</span>
                        <span class="inline-flex items-center gap-1 text-amber-600 font-semibold bg-amber-50 px-2 py-0.5 rounded">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                            PRO
                        </span>
                    </div>
                    <div class="flex justify-between border-t border-border-light pt-3 mt-3">
                        <span class="text-text-secondary">Thời hạn sử dụng đến:</span>
                        <span class="text-text-primary font-bold">
                            {{ $transaction->user->subscription_expires_at ? FormatHelper::dateTime($transaction->user->subscription_expires_at, 'H:i - d/m/Y') : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

            <p class="text-xs text-text-disabled mb-6">Biên lai đã được gửi tới email của bạn.</p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('client.articles.library') }}" class="w-full sm:w-auto px-6 py-3 bg-brand text-white font-medium rounded-lg hover:bg-brand-dark transition-colors flex justify-center items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Bắt đầu học ngay
                </a>
                <a href="{{ route('client.billing') }}" class="w-full sm:w-auto px-6 py-3 bg-white text-text-secondary border border-border-light font-medium rounded-lg hover:bg-app-bg transition-colors">
                    Xem lịch sử thanh toán
                </a>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            const container = document.getElementById('confettiContainer');
            const colors = ['#11A683','#FFD500','#007AFF','#FF5E5E','#8F00FF'];
            for (let i = 0; i < 40; i++) {
                const el = document.createElement('div');
                el.className = 'confetti';
                el.style.cssText = 'left:'+Math.random()*100+'vw;width:'+(6+Math.random()*8)+'px;height:'+(6+Math.random()*8)+'px;background:'+colors[Math.floor(Math.random()*colors.length)]+';border-radius:'+(Math.random()>0.5?'50%':'2px')+';animation-duration:'+(2+Math.random()*3)+'s;animation-delay:'+Math.random()*2+'s;';
                container.appendChild(el);
            }
        </script>
    </x-slot:scripts>
</x-client.layout.app>
