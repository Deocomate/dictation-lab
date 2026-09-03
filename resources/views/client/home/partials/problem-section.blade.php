{{-- SECTION 4: PROBLEM STATEMENT --}}
<section class="py-16 sm:py-20 bg-white">
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
    <h2 class="text-2xl sm:text-3xl font-bold text-text-primary leading-tight">
      {{ $problem['title'] ?? 'Tại sao đọc thụ động không đủ?' }}
    </h2>
    <p class="mt-4 text-base text-text-secondary leading-relaxed max-w-2xl mx-auto">
      {{ $problem['description'] ?? 'Bạn đọc hàng chục bài nhưng vẫn không nhớ cấu trúc câu? Dictation Lab giúp bạn biến kiến thức thành phản xạ thực sự qua chép chính tả song ngữ.' }}
    </p>

    <!-- Pain Points -->
    <div class="grid sm:grid-cols-3 gap-6 mt-10">
      @php
        $painIcons = [
          // Red: warning icon
          '<svg class="w-5 h-5 text-semantic-red" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>',
          // Yellow: clock icon
          '<svg class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
          // Blue: sad face icon
          '<svg class="w-5 h-5 text-semantic-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" /></svg>',
        ];
      @endphp

      @foreach(($problem['items'] ?? []) as $index => $item)
        <div class="{{ $item['bg_class'] ?? 'bg-gray-50' }} rounded-xl p-5 text-left">
          <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-card mb-3">
            {!! $painIcons[$index] ?? '' !!}
          </div>
          <h3 class="text-sm font-semibold text-text-primary">{{ $item['title'] }}</h3>
          <p class="text-sm text-text-secondary mt-1.5 leading-relaxed">{{ $item['description'] }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>
