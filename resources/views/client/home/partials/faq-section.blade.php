{{-- SECTION 11: FAQ --}}
<section class="py-16 sm:py-24 bg-app-bg">
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center reveal">
      <h2 class="text-2xl sm:text-3xl font-bold text-text-primary">Câu hỏi thường gặp</h2>
    </div>

    <div class="mt-10 space-y-3 reveal">
      @foreach(($faqs ?? []) as $faq)
        <details class="group bg-white rounded-xl border border-border-light overflow-hidden">
          <summary class="flex items-center justify-between px-6 py-4 cursor-pointer text-sm font-medium text-text-primary hover:bg-gray-50 transition-colors duration-200 list-none">
            <span>{{ $faq['question'] }}</span>
            <svg class="w-4 h-4 text-text-secondary flex-shrink-0 transition-transform duration-200 group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
          </summary>
          <div class="px-6 pb-4 text-sm text-text-secondary leading-relaxed">
            {{ $faq['answer'] }}
          </div>
        </details>
      @endforeach
    </div>
  </div>
</section>
