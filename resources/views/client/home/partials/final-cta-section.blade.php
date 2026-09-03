{{-- SECTION 12: FINAL CTA --}}
<section class="py-16 sm:py-24 bg-white">
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-text-primary leading-tight">
      {{ $finalCta['title_line1'] ?? 'Sẵn sàng chinh phục' }}<br />
      <span class="text-brand">{{ $finalCta['title_line2'] ?? 'tiếng Anh mỗi ngày?' }}</span>
    </h2>
    <p class="mt-4 text-base text-text-secondary max-w-xl mx-auto leading-relaxed">
      {{ $finalCta['description'] ?? 'Đăng ký miễn phí ngay hôm nay. Không cần thẻ tín dụng. Bắt đầu chép chính tả bài mẫu đầu tiên trong vòng 30 giây.' }}
    </p>
    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
      <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-3.5 bg-brand text-white font-semibold rounded-lg hover:bg-brand-dark transition-colors duration-200 cursor-pointer focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2 text-base">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.58-5.84a14.927 14.927 0 00-2.58 5.84m2.58-5.84L18 2.76M2 16.15l2.04-2.04" />
        </svg>
        Bắt đầu miễn phí ngay
      </a>
      <a href="{{ route('client.home.pricing-contact') }}" class="text-sm font-medium text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">
        Xem bảng giá chi tiết
      </a>
    </div>
  </div>
</section>
