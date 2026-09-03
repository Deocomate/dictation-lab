@php
  $seo = $homeData['seo'] ?? [];
  $pricing = $homeData['pricing'] ?? [];
  $planFit = $homeData['plan_fit'] ?? [];
  $faqs = $homeData['faqs'] ?? [];
  $contact = $homeData['contact'] ?? [];
  $finalCta = $homeData['final_cta'] ?? [];
@endphp

<x-client.layout.marketing-page
  title="Gói & Liên hệ - Dictation Lab"
  :description="$seo['description'] ?? ''"
>
  <section class="pt-28 pb-14 sm:pt-32 sm:pb-16 bg-white border-b border-border-light">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
      <span class="inline-flex items-center px-3 py-1 bg-purple-50 text-semantic-purple text-xs font-semibold rounded-full uppercase tracking-wide">Gói & liên hệ</span>
      <h1 class="mt-4 text-3xl sm:text-4xl font-bold text-text-primary leading-tight">Chọn gói phù hợp mục tiêu và kết nối trực tiếp với đội ngũ thực hiện</h1>
      <p class="mt-4 text-base text-text-secondary leading-relaxed max-w-3xl mx-auto">
        Trang này tổng hợp bảng giá, đối tượng phù hợp từng gói và đầy đủ thông tin liên hệ để bạn ra quyết định nhanh chóng.
      </p>
    </div>
  </section>

  @include('client.home.partials.pricing-section')
  @include('client.home.partials.plan-fit-section')
  @include('client.home.partials.faq-section')
  @include('client.home.partials.contact-section')
  @include('client.home.partials.final-cta-section')
</x-client.layout.marketing-page>
