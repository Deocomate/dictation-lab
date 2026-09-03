{{-- SECTION 6: HOW IT WORKS (3-Step Process) --}}
<section id="how-it-works" class="py-16 sm:py-24 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center reveal">
      <h2 class="text-2xl sm:text-3xl font-bold text-text-primary">Cách hoạt động</h2>
      <p class="mt-3 text-base text-text-secondary max-w-xl mx-auto leading-relaxed">
        Chỉ 3 bước đơn giản để bắt đầu luyện chép chính tả song ngữ mỗi ngày.
      </p>
    </div>

    <div class="grid md:grid-cols-3 gap-8 lg:gap-12 mt-12 relative">
      <!-- Connection Line (Desktop only) -->
      <div class="hidden md:block absolute top-12 left-[16.67%] right-[16.67%] h-0.5 bg-border-light" aria-hidden="true"></div>

      @foreach(($howItWorks ?? []) as $index => $step)
        @php
          $delays = ['0.05s', '0.15s', '0.25s'];
        @endphp
        <div class="text-center reveal" style="transition-delay: {{ $delays[$index] ?? '0.05s' }}">
          <div class="w-14 h-14 bg-brand text-white rounded-2xl flex items-center justify-center mx-auto text-xl font-bold shadow-card relative z-10">{{ $index + 1 }}</div>
          <h3 class="text-base font-semibold text-text-primary mt-5">{{ $step['title'] }}</h3>
          <p class="text-sm text-text-secondary mt-2 leading-relaxed max-w-xs mx-auto">{{ $step['description'] }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>
