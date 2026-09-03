{{-- SECTION 5: 3 CORE FEATURES --}}
<section id="features" class="py-16 sm:py-24 bg-app-bg">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center reveal">
      <h2 class="text-2xl sm:text-3xl font-bold text-text-primary leading-tight">
        3 tính năng cốt lõi cho luyện tập mỗi ngày
      </h2>
      <p class="mt-3 text-base text-text-secondary max-w-2xl mx-auto leading-relaxed">
        Chép chính tả song ngữ, khám phá đa chủ đề và lưu từ vựng trong ngữ cảnh — tất cả trong một nền tảng.
      </p>
    </div>

    @php
      $featureStyles = [
        ['bg' => 'bg-brand-light', 'text' => 'text-brand', 'pill' => 'bg-brand-light text-brand'],
        ['bg' => 'bg-blue-50', 'text' => 'text-semantic-blue', 'pill' => 'bg-blue-50 text-semantic-blue'],
        ['bg' => 'bg-purple-50', 'text' => 'text-semantic-purple', 'pill' => 'bg-purple-50 text-semantic-purple'],
      ];
      $featureIcons = [
        '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />',
        '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />',
        '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />',
      ];
    @endphp

    <div class="grid md:grid-cols-3 gap-6 lg:gap-8 mt-12">
      @foreach(($features ?? []) as $index => $feature)
        @php $style = $featureStyles[$index] ?? $featureStyles[0]; @endphp
        <div class="feature-card bg-white rounded-2xl p-6 lg:p-8 shadow-card border border-border-light cursor-pointer reveal relative overflow-hidden" style="transition-delay: {{ 0.05 + ($index * 0.1) }}s">
          @if(($feature['badge'] ?? '') === 'PRO')
            <div class="absolute top-4 right-4">
              <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-yellow-50 border border-yellow-200 rounded-full text-[11px] font-semibold text-yellow-700">PRO</span>
            </div>
          @endif

          <div class="w-12 h-12 {{ $style['bg'] }} rounded-xl flex items-center justify-center mb-5">
            <svg class="w-6 h-6 {{ $style['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
              {!! $featureIcons[$index] ?? $featureIcons[0] !!}
            </svg>
          </div>

          @if(!empty($feature['badge']))
            <span class="band-pill {{ $style['pill'] }} mb-3">{{ $feature['badge'] }}</span>
          @endif

          <h3 class="text-lg font-semibold text-text-primary mt-3">{{ $feature['title'] }}</h3>
          <p class="text-sm text-text-secondary mt-2 leading-relaxed">{{ $feature['description'] }}</p>

          <ul class="mt-4 space-y-2 text-sm text-text-secondary">
            @foreach(($feature['highlights'] ?? []) as $highlight)
              <li class="flex items-start gap-2">
                <svg class="w-4 h-4 {{ $style['text'] }} mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                <span>{{ $highlight }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      @endforeach
    </div>
  </div>
</section>
