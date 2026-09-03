{{-- SECTION 2: HERO — Headline + Visual Demo --}}
<section class="pt-28 pb-16 sm:pt-32 sm:pb-20 lg:pt-40 lg:pb-28 bg-white relative overflow-hidden">
  <!-- Subtle gradient blob background -->
  <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-brand-light rounded-full opacity-40 blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none" aria-hidden="true"></div>
  <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-50 rounded-full opacity-30 blur-3xl translate-y-1/2 -translate-x-1/3 pointer-events-none" aria-hidden="true"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

      <!-- Left: Text Content -->
      <div class="max-w-xl">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-brand-light rounded-full mb-6 animate-fade-in">
          <span class="w-2 h-2 bg-brand rounded-full animate-pulse-soft" aria-hidden="true"></span>
          <span class="text-xs font-semibold text-brand uppercase tracking-wide">{{ $hero['badge'] ?? 'AI-Powered Learning' }}</span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight tracking-tight text-text-primary animate-fade-in-up" style="animation-delay: 0.1s">
          @foreach(($hero['title_lines'] ?? ['Chép chính tả song ngữ,', 'tăng phản xạ tiếng Anh', 'qua muscle memory.']) as $index => $line)
            @if($index === 1)
              <span class="text-brand">{{ $line }}</span><br />
            @elseif($loop->last)
              {{ $line }}
            @else
              {{ $line }}<br />
            @endif
          @endforeach
        </h1>

        <p class="mt-5 text-lg text-text-secondary leading-relaxed animate-fade-in-up" style="animation-delay: 0.2s">
          {{ $hero['description'] ?? '' }}
        </p>

        <!-- CTA Group -->
        <div class="mt-8 flex flex-col sm:flex-row items-start sm:items-center gap-4 animate-fade-in-up" style="animation-delay: 0.3s">
          <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-brand text-white font-semibold rounded-lg hover:bg-brand-dark transition-colors duration-200 cursor-pointer focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2 text-base">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.58-5.84a14.927 14.927 0 00-2.58 5.84m2.58-5.84L18 2.76M2 16.15l2.04-2.04" />
            </svg>
            Bắt đầu học miễn phí
          </a>
          @if($homeData['demo_video_url'] ?? null)
          <button type="button" @click="$dispatch('open-demo-video')" class="inline-flex items-center gap-2 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer group">
            <span class="w-10 h-10 flex items-center justify-center rounded-full border-2 border-border-light group-hover:border-brand transition-colors duration-200">
              <svg class="w-4 h-4 text-text-secondary group-hover:text-brand transition-colors" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M8 5v14l11-7z" />
              </svg>
            </span>
            Xem demo
          </button>
          @else
          <a href="{{ route('client.home.features') }}#demo" class="inline-flex items-center gap-2 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer group">
            <span class="w-10 h-10 flex items-center justify-center rounded-full border-2 border-border-light group-hover:border-brand transition-colors duration-200">
              <svg class="w-4 h-4 text-text-secondary group-hover:text-brand transition-colors" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M8 5v14l11-7z" />
              </svg>
            </span>
            Xem demo
          </a>
          @endif
        </div>

        <!-- Trust Signals -->
        <div class="mt-8 flex items-center gap-6 text-sm text-text-secondary animate-fade-in-up" style="animation-delay: 0.4s">
          @foreach(($hero['trust_signals'] ?? []) as $index => $signal)
            <div class="flex items-center gap-1.5 @if($index === 2) max-sm:hidden @endif">
              <svg class="w-4 h-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ $signal }}</span>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Right: Dictation Demo Card -->
      <div class="animate-fade-in-up" style="animation-delay: 0.35s">
        <div class="bg-white rounded-2xl shadow-card border border-border-light p-6 sm:p-8 relative">
          <div class="flex items-center gap-1.5 mb-5">
            <span class="w-3 h-3 rounded-full bg-semantic-red opacity-50" aria-hidden="true"></span>
            <span class="w-3 h-3 rounded-full bg-semantic-yellow opacity-50" aria-hidden="true"></span>
            <span class="w-3 h-3 rounded-full bg-brand opacity-50" aria-hidden="true"></span>
            <span class="ml-3 text-xs text-text-disabled font-medium">Dictation Lab</span>
          </div>

          <p class="text-sm text-text-secondary mb-1">Nghĩa tiếng Việt:</p>
          <p class="text-lg font-semibold text-text-primary mb-6">Nền kinh tế đang tăng trưởng.</p>

          <p class="text-sm text-text-secondary mb-2">Gõ câu tiếng Anh:</p>
          <div class="font-mono text-base leading-relaxed p-4 bg-app-bg rounded-xl border border-border-light">
            <span class="text-brand font-medium">The economy is growing</span><span class="text-text-disabled animate-pulse">|</span><span class="text-gray-300">.</span>
          </div>

          <div class="mt-6 pt-4 border-t border-border-light flex items-center justify-between text-xs text-text-secondary">
            <span>Câu 1 / 4</span>
            <div class="flex items-center gap-3">
              <span>WPM <strong class="text-text-primary">42</strong></span>
              <span>Chính xác <strong class="text-brand">98%</strong></span>
            </div>
          </div>
          <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full w-1/4 bg-brand rounded-full"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
