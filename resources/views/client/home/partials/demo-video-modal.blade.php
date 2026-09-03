@php
  $demoVideoUrl = $homeData['demo_video_url'] ?? null;
@endphp

@if($demoVideoUrl)
  <div
    x-data="{ open: false }"
    x-on:open-demo-video.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-cloak
  >
    <div
      x-show="open"
      x-transition.opacity
      class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm px-4"
      role="dialog"
      aria-modal="true"
      aria-label="Video demo Dictation Lab"
    >
      <div class="absolute inset-0" aria-hidden="true" @click="open = false"></div>
      <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-4xl"
      >
        <button
          type="button"
          @click="open = false"
          class="absolute -top-12 right-0 inline-flex items-center gap-2 text-sm font-semibold text-white/90 hover:text-white focus-visible:ring-2 focus-visible:ring-white rounded-lg px-2 py-1 cursor-pointer"
        >
          Đóng
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>
        <div class="aspect-video overflow-hidden rounded-2xl bg-black shadow-float ring-1 ring-white/10">
          <template x-if="open">
            <iframe
              src="{{ $demoVideoUrl }}?autoplay=1&rel=0"
              class="h-full w-full border-0"
              title="Video demo Dictation Lab"
              allow="autoplay; encrypted-media; picture-in-picture; fullscreen"
              allowfullscreen
            ></iframe>
          </template>
        </div>
      </div>
    </div>
  </div>
@endif
