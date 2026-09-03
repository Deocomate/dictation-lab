<section class="py-16 sm:py-24 bg-white">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center reveal">
      <h2 class="text-2xl sm:text-3xl font-bold text-text-primary">Tại sao nên dùng web này thay vì các nền tảng khác?</h2>
      <p class="mt-3 text-base text-text-secondary max-w-2xl mx-auto leading-relaxed">
        Không chỉ là nơi đọc bài mẫu, đây là hệ thống học có vòng lặp phản hồi rõ ràng để tăng band thực tế.
      </p>
    </div>

    <div class="grid md:grid-cols-3 gap-6 mt-12">
      @foreach(($aboutPage['why_choose'] ?? []) as $index => $item)
        <div class="bg-app-bg border border-border-light rounded-2xl p-6 shadow-card reveal" style="transition-delay: {{ 0.1 * ($index + 1) }}s">
          <h3 class="text-lg font-semibold text-text-primary">{{ $item['title'] }}</h3>
          <p class="mt-3 text-sm text-text-secondary leading-relaxed">{{ $item['description'] }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>
