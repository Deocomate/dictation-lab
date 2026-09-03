<section class="py-16 sm:py-24 bg-white">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center reveal">
      <h2 class="text-2xl sm:text-3xl font-bold text-text-primary">Ai phù hợp với gói nào?</h2>
      <p class="mt-3 text-base text-text-secondary max-w-2xl mx-auto leading-relaxed">
        Chọn gói dựa trên mục tiêu tăng band và cường độ luyện tập của bạn.
      </p>
    </div>

    <div class="space-y-4 mt-10">
      @foreach(($planFit ?? []) as $index => $item)
        <div class="bg-app-bg border border-border-light rounded-2xl p-5 sm:p-6 reveal" style="transition-delay: {{ 0.1 * ($index + 1) }}s">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-lg font-semibold text-text-primary">{{ $item['plan'] }}</h3>
            <span class="inline-flex px-3 py-1 rounded-full bg-white border border-border-light text-xs font-medium text-text-secondary">Gợi ý sử dụng</span>
          </div>
          <p class="mt-3 text-sm text-text-secondary leading-relaxed"><strong class="text-text-primary">Phù hợp với:</strong> {{ $item['fit_for'] }}</p>
          <p class="mt-2 text-sm text-text-secondary leading-relaxed"><strong class="text-text-primary">Tốt nhất khi:</strong> {{ $item['best_when'] }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>
