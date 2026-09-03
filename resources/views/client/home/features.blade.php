@php
  $seo = $homeData['seo'] ?? [];
  $features = $homeData['features'] ?? [];
  $howItWorks = $homeData['how_it_works'] ?? [];
  $featureExperience = $homeData['feature_experience'] ?? [];
  $finalCta = $homeData['final_cta'] ?? [];
@endphp

<x-client.layout.marketing-page
  title="Chức năng chi tiết - Dictation Lab"
  :description="$seo['description'] ?? ''"
>
  <section class="pt-28 pb-14 sm:pt-32 sm:pb-16 bg-white border-b border-border-light">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
      <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-semantic-blue text-xs font-semibold rounded-full uppercase tracking-wide">Chức năng chi tiết</span>
      <h1 class="mt-4 text-3xl sm:text-4xl font-bold text-text-primary leading-tight">Toàn bộ trải nghiệm học tập được hiển thị trực quan, rõ ràng</h1>
      <p class="mt-4 text-base text-text-secondary leading-relaxed max-w-3xl mx-auto">
        Từ chép chính tả song ngữ, thư viện đa chủ đề, sổ từ vựng highlight-to-save đến AI dịch từ (Pro).
        Mỗi chức năng đều có vai trò rõ ràng trong lộ trình tăng phản xạ tiếng Anh.
      </p>
    </div>
  </section>

  @include('client.home.partials.features-section')
  @include('client.home.partials.feature-experience-section')
  @include('client.home.partials.how-it-works-section')
  @include('client.home.partials.demo-section')
  @include('client.home.partials.color-system-section')
  @include('client.home.partials.final-cta-section')
  @include('client.home.partials.demo-video-modal')
</x-client.layout.marketing-page>
