@php
  $seo = $homeData['seo'] ?? [];
  $socialProof = $homeData['social_proof'] ?? [];
  $problem = $homeData['problem_section'] ?? [];
  $aboutPage = $homeData['about_page'] ?? [];
  $finalCta = $homeData['final_cta'] ?? [];
@endphp

<x-client.layout.marketing-page
  title="Giới thiệu hệ thống - Dictation Lab"
  :description="$seo['description'] ?? ''"
>
  <section class="pt-28 pb-14 sm:pt-32 sm:pb-16 bg-white border-b border-border-light">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
      <span class="inline-flex items-center px-3 py-1 bg-brand-light text-brand text-xs font-semibold rounded-full uppercase tracking-wide">Giới thiệu hệ thống</span>
      <h1 class="mt-4 text-3xl sm:text-4xl font-bold text-text-primary leading-tight">Nền tảng chép chính tả song ngữ đa chủ đề</h1>
      <p class="mt-4 text-base text-text-secondary leading-relaxed max-w-3xl mx-auto">
        Trang này giúp bạn hiểu đầy đủ ứng dụng giải quyết vấn đề gì, vì sao nên chọn Dictation Lab,
        và thông tin đội ngũ thực hiện sản phẩm cuối môn.
      </p>
    </div>
  </section>

  @include('client.home.partials.social-proof-section')
  @include('client.home.partials.about-details-section')
  @include('client.home.partials.problem-section')
  @include('client.home.partials.why-choose-section')
  @include('client.home.partials.founder-section')
  @include('client.home.partials.final-cta-section')
</x-client.layout.marketing-page>
