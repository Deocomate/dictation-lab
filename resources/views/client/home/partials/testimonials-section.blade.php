{{-- SECTION 10: TESTIMONIALS --}}
<section id="testimonials" class="py-16 sm:py-24 bg-white">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center reveal">
      <h2 class="text-2xl sm:text-3xl font-bold text-text-primary">Học viên nói gì?</h2>
      <p class="mt-3 text-base text-text-secondary max-w-xl mx-auto leading-relaxed">
        Hơn 10,000 học viên đã cải thiện phản xạ tiếng Anh với Dictation Lab.
      </p>
    </div>

    <div class="grid md:grid-cols-3 gap-6 mt-12">
      @foreach(($testimonials ?? []) as $index => $testimonial)
        @php
          $delays = ['0.05s', '0.15s', '0.25s'];
        @endphp
        <div class="bg-app-bg rounded-2xl p-6 reveal" style="transition-delay: {{ $delays[$index] ?? '0.05s' }}">
          {{-- 5-Star Rating --}}
          <div class="flex items-center gap-0.5 mb-4" aria-label="5 sao">
            @for($i = 0; $i < 5; $i++)
              <svg class="w-4 h-4 text-semantic-yellow" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" /></svg>
            @endfor
          </div>

          <p class="text-sm text-text-primary leading-relaxed">
            "{!! $testimonial['content'] !!}"
          </p>

          <div class="mt-4 flex items-center gap-3">
            <div class="w-9 h-9 {{ $testimonial['avatar_bg'] ?? 'bg-brand-light' }} rounded-full flex items-center justify-center {{ $testimonial['avatar_text'] ?? 'text-brand' }} font-semibold text-sm">{{ $testimonial['initial'] }}</div>
            <div>
              <p class="text-sm font-medium text-text-primary">{{ $testimonial['name'] }}</p>
              <p class="text-xs text-text-secondary">{{ $testimonial['role'] }}</p>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
