<x-client.layout.dashboard title="Thư viện bài viết" activePage="articles">
  <x-slot:head>
    <style>
      .article-card{transition:box-shadow 0.2s, transform 0.2s;}
      .article-card:hover{box-shadow:0 4px 6px rgba(0,0,0,0.05),0 10px 15px rgba(0,0,0,0.1);transform:translateY(-2px);}
      .filter-chip{transition:all 0.15s;}
      .filter-chip.active{background:#11A683;color:#fff;border-color:#11A683;}
      @media(prefers-reduced-motion:reduce){.article-card{transition:none;}.article-card:hover{transform:none;}.filter-chip{transition:none;}}
    </style>
  </x-slot:head>

  <x-slot:headerContent>
    <div>
      <h1 class="text-lg font-bold text-text-primary">Thư viện bài viết</h1>
      <p class="text-xs text-text-secondary">{{ $articles->total() }} bài viết chép chính tả</p>
    </div>
  </x-slot:headerContent>
  <x-slot:headerActions>
    <form method="GET" action="{{ route('client.articles.library') }}" class="hidden sm:flex items-center gap-2">
      @foreach($filters as $k => $v)
        @if($k !== 'search')
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
      @endforeach
      <div class="relative">
        <input name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm kiếm bài viết..."
          class="pl-8 pr-4 py-2 bg-app-bg border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled w-56" />
        <svg class="w-4 h-4 text-text-disabled absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </div>
    </form>
  </x-slot:headerActions>

  {{-- Category filter chips --}}
  <div class="flex items-center gap-2 mb-5 overflow-x-auto pb-1 flex-wrap">
    <a href="{{ route('client.articles.library', array_merge(request()->except(['category', 'page']), [])) }}"
       class="filter-chip flex-shrink-0 px-3 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer {{ empty($filters['category']) ? 'active border-brand bg-brand text-white' : 'border-border-light bg-white text-text-secondary hover:text-brand hover:border-brand' }}">
      Tất cả
    </a>
    @foreach($categories as $cat)
      <a href="{{ route('client.articles.library', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}"
         class="filter-chip flex-shrink-0 px-3 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer {{ ($filters['category'] ?? '') === $cat->slug ? 'active border-brand bg-brand text-white' : 'border-border-light bg-white text-text-secondary hover:text-brand hover:border-brand' }}">
        {{ $cat->name }}
      </a>
    @endforeach
    {{-- Access filter --}}
    <div class="ml-auto flex items-center gap-2">
      <a href="{{ route('client.articles.library', array_merge(request()->except(['access', 'page']), [])) }}"
         class="filter-chip flex-shrink-0 px-3 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer {{ empty($filters['access']) ? 'active' : 'border-border-light bg-white text-text-secondary hover:text-brand hover:border-brand' }}">
        Tất cả
      </a>
      <a href="{{ route('client.articles.library', array_merge(request()->except(['access', 'page']), ['access' => 'free'])) }}"
         class="filter-chip flex-shrink-0 px-3 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer {{ ($filters['access'] ?? '') === 'free' ? 'active' : 'border-border-light bg-white text-text-secondary hover:text-brand hover:border-brand' }}">
        Miễn phí
      </a>
      <a href="{{ route('client.articles.library', array_merge(request()->except(['access', 'page']), ['access' => 'pro'])) }}"
         class="filter-chip flex-shrink-0 px-3 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer {{ ($filters['access'] ?? '') === 'pro' ? 'active' : 'border-border-light bg-white text-text-secondary hover:text-brand hover:border-brand' }}">
        Pro
      </a>
    </div>
  </div>

  {{-- Articles grid --}}
  @if($articles->isEmpty())
    <div class="bg-white rounded-xl border border-border-light p-10 text-center">
      <svg class="w-12 h-12 text-text-disabled mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
      <p class="text-sm text-text-disabled">Không tìm thấy bài viết phù hợp.</p>
    </div>
  @else
    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
      @foreach($articles as $article)
        @php
          $isPro = $article->is_premium;
          $userIsPro = auth()->user()->isPro();
          $locked = $isPro && !$userIsPro;
          $sentenceCount = $article->sentenceCount();
        @endphp
        <div class="article-card bg-white rounded-xl border border-border-light overflow-hidden {{ $locked ? 'opacity-80' : '' }}">
          {{-- Cover image --}}
          <div class="h-28 relative overflow-hidden group">
            @if($article->image_path)
              <img src="{{ Storage::disk('public')->url($article->image_path) }}"
                   alt="{{ $article->title }}"
                   class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
            @else
              <div class="w-full h-full bg-gradient-to-br from-brand-light to-brand/10 flex items-center justify-center">
                <svg class="w-10 h-10 text-brand/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
              </div>
            @endif
            @if($isPro)
              <div class="absolute top-2 right-2 flex items-center gap-1 px-1.5 py-0.5 bg-white rounded-full shadow-card">
                <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span class="text-xs font-bold text-text-primary">Pro</span>
              </div>
            @endif
          </div>

          {{-- Card body --}}
          <div class="p-4">
            <div class="flex items-center gap-2 mb-2 flex-wrap">
              @foreach($article->categories as $cat)
                <span class="px-2 py-0.5 bg-brand-light text-brand text-xs font-semibold rounded">{{ $cat->name }}</span>
              @endforeach
              <span class="ml-auto text-xs text-text-disabled">{{ $sentenceCount }} câu</span>
            </div>
            <h3 class="font-semibold text-sm text-text-primary leading-snug mb-1">{{ $article->title }}</h3>
            @if($article->excerpt)
              <p class="text-xs text-text-secondary line-clamp-2">{{ $article->excerpt }}</p>
            @endif

            <div class="mt-3">
              @if($locked)
                <a href="{{ route('client.checkout') }}" class="flex items-center justify-center gap-1.5 w-full py-2 bg-app-bg border border-border-light text-xs font-semibold text-text-secondary rounded-lg hover:bg-brand-light hover:text-brand hover:border-brand transition-colors">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                  Nâng cấp Pro để truy cập
                </a>
              @else
                <a href="{{ route('client.learning.dictation', $article) }}" class="flex items-center justify-center gap-1.5 w-full py-2 bg-brand text-white text-xs font-semibold rounded-lg hover:bg-brand-dark transition-colors">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  Chép chính tả ngay
                </a>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-6">{{ $articles->withQueryString()->links() }}</div>
  @endif
</x-client.layout.dashboard>
