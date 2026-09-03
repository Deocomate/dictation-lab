{{-- SECTION 3: SOCIAL PROOF BAR --}}
<section class="py-8 bg-app-bg border-y border-border-light">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 sm:gap-12 text-center">
      @foreach(($socialProof ?? []) as $index => $stat)
        @if($index > 0)
          <div class="hidden sm:block w-px h-10 bg-border-light" aria-hidden="true"></div>
        @endif
        <div>
          <p class="text-2xl font-bold {{ $loop->last ? 'text-brand' : 'text-text-primary' }}">{{ $stat['value'] }}</p>
          <p class="text-sm text-text-secondary mt-0.5">{{ $stat['label'] }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>
