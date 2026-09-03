<section class="py-16 sm:py-24 bg-app-bg">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
    <div class="bg-white rounded-2xl border border-border-light shadow-card p-7 sm:p-10">
      <h2 class="text-2xl sm:text-3xl font-bold text-text-primary leading-tight">
        {{ $aboutPage['application_intro']['title'] ?? 'Ứng dụng này giúp bạn luyện chép chính tả song ngữ hiệu quả hơn.' }}
      </h2>
      <p class="mt-4 text-base text-text-secondary leading-relaxed">
        {{ $aboutPage['application_intro']['description'] ?? '' }}
      </p>

      <div class="grid md:grid-cols-3 gap-4 mt-8">
        @foreach(($aboutPage['application_intro']['highlights'] ?? []) as $highlight)
          <div class="bg-app-bg border border-border-light rounded-xl p-4">
            <p class="text-sm text-text-primary leading-relaxed">{{ $highlight }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
