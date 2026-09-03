<section class="py-16 sm:py-24 bg-white">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center reveal">
      <h2 class="text-2xl sm:text-3xl font-bold text-text-primary">Người dùng sẽ trải nghiệm những gì?</h2>
      <p class="mt-3 text-base text-text-secondary max-w-2xl mx-auto leading-relaxed">
        Mỗi chức năng đều có đầu ra rõ ràng để bạn biết mình đang tiến bộ ở đâu, cần tối ưu điểm nào.
      </p>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mt-12">
      @foreach(($featureExperience ?? []) as $index => $item)
        <div class="bg-app-bg border border-border-light rounded-2xl p-6 shadow-card reveal" style="transition-delay: {{ 0.1 * ($index + 1) }}s">
          <h3 class="text-lg font-semibold text-text-primary">{{ $item['title'] }}</h3>
          <p class="mt-2 text-sm text-text-secondary leading-relaxed">{{ $item['description'] }}</p>
          <ul class="mt-4 space-y-2">
            @foreach(($item['experience'] ?? []) as $experience)
              <li class="flex items-start gap-2 text-sm text-text-secondary">
                <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                <span>{{ $experience }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      @endforeach
    </div>
  </div>
</section>
