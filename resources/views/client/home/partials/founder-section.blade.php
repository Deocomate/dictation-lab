@php
  $founder = $aboutPage['founder'] ?? [];
@endphp

<section class="py-16 sm:py-24 bg-app-bg">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
    <div class="bg-white rounded-2xl border border-border-light shadow-card p-7 sm:p-10">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-text-secondary">Thông tin đội ngũ thực hiện</p>
          <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-text-primary">{{ $founder['name'] ?? 'Founder' }}</h2>
          <p class="mt-1 text-sm text-brand font-medium">{{ $founder['role'] ?? '' }}</p>
        </div>
        <div class="w-16 h-16 rounded-2xl bg-brand-light text-brand flex items-center justify-center text-xl font-bold">18</div>
      </div>

      <div class="grid sm:grid-cols-2 gap-3 mt-6">
        <div class="bg-app-bg border border-border-light rounded-xl p-4">
          <p class="text-xs uppercase tracking-wide text-text-secondary font-semibold">Nhóm thực hiện</p>
          <p class="mt-1 text-sm text-text-primary font-medium">{{ $founder['team_name'] ?? '-' }}</p>
        </div>
        <div class="bg-app-bg border border-border-light rounded-xl p-4">
          <p class="text-xs uppercase tracking-wide text-text-secondary font-semibold">Co-Founder</p>
          <p class="mt-1 text-sm text-text-primary font-medium">{{ $founder['co_founder'] ?? '-' }}</p>
        </div>
        <div class="bg-app-bg border border-border-light rounded-xl p-4">
          <p class="text-xs uppercase tracking-wide text-text-secondary font-semibold">Môn học</p>
          <p class="mt-1 text-sm text-text-primary font-medium">{{ $founder['course'] ?? '-' }}</p>
        </div>
        <div class="bg-app-bg border border-border-light rounded-xl p-4">
          <p class="text-xs uppercase tracking-wide text-text-secondary font-semibold">Loại sản phẩm</p>
          <p class="mt-1 text-sm text-text-primary font-medium">{{ $founder['project_type'] ?? '-' }}</p>
        </div>
      </div>

      <p class="mt-6 text-base text-text-primary leading-relaxed">
        "{{ $founder['quote'] ?? '' }}"
      </p>

      <ul class="mt-6 space-y-3">
        @foreach(($founder['story'] ?? []) as $story)
          <li class="flex items-start gap-3 text-sm text-text-secondary leading-relaxed">
            <svg class="w-4 h-4 text-brand mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
            <span>{{ $story }}</span>
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</section>
