{{-- SECTION 9: PRICING --}}
<section id="pricing" class="py-16 sm:py-24 bg-app-bg">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center reveal">
      <h2 class="text-2xl sm:text-3xl font-bold text-text-primary">Bảng giá đơn giản, giá trị rõ ràng</h2>
      <p class="mt-3 text-base text-text-secondary max-w-xl mx-auto leading-relaxed">
        Bắt đầu miễn phí. Nâng cấp Pro khi bạn muốn chép không giới hạn và dùng AI.
      </p>
    </div>

    <!-- Đổi max-w-3xl thành max-w-6xl và thêm lg:grid-cols-3 để UI không bị vỡ nếu Admin tạo 2 gói Pro trở lên -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 mt-12 max-w-6xl mx-auto items-stretch">

      {{-- Free Plan --}}
      <div class="bg-white rounded-2xl shadow-card border border-border-light p-6 lg:p-8 reveal flex flex-col" style="transition-delay: 0.05s">
        <div>
          <h3 class="text-lg font-semibold text-text-primary">{{ $pricing['free']['name'] ?? 'Free' }}</h3>
          <p class="text-sm text-text-secondary mt-1">Hoàn hảo để bắt đầu</p>
          <div class="mt-5">
            <span class="text-4xl font-bold text-text-primary">{{ $pricing['free']['price_label'] ?? '0đ' }}</span>
            <span class="text-sm text-text-secondary ml-1">{{ $pricing['free']['duration_label'] ?? '/mãi mãi' }}</span>
          </div>
        </div>

        <div class="mt-auto flex flex-col">
          <a href="{{ route('register') }}" class="mt-6 w-full inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 text-text-primary text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200 cursor-pointer focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2">
            Bắt đầu miễn phí
          </a>
          <ul class="mt-6 space-y-3 text-sm text-text-secondary">
            @foreach(($pricing['free']['features'] ?? []) as $feature)
              <li class="flex items-start gap-2.5">
                @if($feature['included'])
                  <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                  <span>{{ $feature['label'] }}</span>
                @else
                  <svg class="w-4 h-4 text-text-disabled mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                  <span class="text-text-disabled">{{ $feature['label'] }}</span>
                @endif
              </li>
            @endforeach
          </ul>
        </div>
      </div>

      {{-- Pro Plans --}}
      @foreach(($pricing['pro'] ?? []) as $index => $proPlan)
        <div class="{{ $index === 0 ? 'pricing-card-popular' : 'border border-border-light' }} bg-white rounded-2xl shadow-card p-6 lg:p-8 reveal flex flex-col relative" style="transition-delay: {{ 0.15 + ($index * 0.1) }}s">

          <!-- Chỉ gắn Badge cho gói Pro đầu tiên -->
          @if($index === 0)
          <div class="absolute -top-3 left-1/2 -translate-x-1/2">
            <span class="inline-flex items-center gap-1 px-3 py-1 bg-brand text-white text-xs font-semibold rounded-full shadow-card whitespace-nowrap">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" /></svg>
              Phổ biến nhất
            </span>
          </div>
          @endif

          <div>
            <h3 class="text-lg font-semibold text-text-primary">{{ $proPlan['name'] }}</h3>
            <p class="text-sm text-text-secondary mt-1">Mở khóa toàn bộ tiềm năng</p>
            <div class="mt-5 flex items-end">
              <span class="text-4xl font-bold text-text-primary">{{ $proPlan['price_label'] }}</span>
              <span class="text-sm text-text-secondary ml-1 mb-1">{{ $proPlan['duration_label'] }}</span>
            </div>
          </div>

          <div class="mt-auto flex flex-col">
            <!-- Nút nâng cấp chuyển thẳng tới màn hình checkout với params plan = ID -->
            <a href="{{ route('client.checkout', ['plan' => $proPlan['id']]) }}" class="mt-6 w-full inline-flex items-center justify-center px-5 py-2.5 bg-brand text-white text-sm font-medium rounded-lg hover:bg-brand-dark transition-colors duration-200 cursor-pointer focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2">
              Nâng cấp {{ $proPlan['name'] }}
            </a>

            <ul class="mt-6 space-y-3 text-sm text-text-secondary">
              @foreach(($proPlan['features'] ?? [
                '1000+ bài báo/truyện song ngữ',
                'Chép chính tả không giới hạn',
                'Lưu từ vựng không giới hạn',
                'AI dịch từ vựng & giải thích câu',
              ]) as $proFeature)
              <li class="flex items-start gap-2.5">
                <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                <span>{{ $proFeature }}</span>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
      @endforeach

    </div>
  </div>
</section>
