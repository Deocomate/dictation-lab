@php
  $seo = $homeData['seo'] ?? [];
  $hero = $homeData['hero'] ?? [];
  $socialProof = $homeData['social_proof'] ?? [];
  $finalCta = $homeData['final_cta'] ?? [];
@endphp

<x-client.layout.marketing-page
  :title="$seo['title'] ?? 'Dictation Lab'"
  :description="$seo['description'] ?? ''"
>
  @include('client.home.partials.hero-section')
  @include('client.home.partials.social-proof-section')
  @include('client.home.partials.overview-pages-section')
  @include('client.home.partials.final-cta-section')
  @include('client.home.partials.demo-video-modal')
</x-client.layout.marketing-page>
