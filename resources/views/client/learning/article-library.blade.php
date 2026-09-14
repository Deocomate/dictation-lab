<x-client.layout.dashboard title="Thư viện bài viết" activePage="articles">
  <x-slot:head>
    <style>
      .scrollbar-none::-webkit-scrollbar { display: none; }
      .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
      .article-card {
        transition: box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1),
                    transform 0.2s cubic-bezier(0.16, 1, 0.3, 1),
                    border-color 0.2s ease;
      }
      .article-card:hover {
        transform: translateY(-3px);
      }
      @media (prefers-reduced-motion: reduce) {
        .article-card { transition: none; }
        .article-card:hover { transform: none; }
      }
    </style>
  </x-slot:head>

  <x-slot:headerContent>
    <div>
      <h1 class="text-base sm:text-lg font-bold text-text-primary tracking-tight">Thư viện bài viết</h1>
      <p class="text-xs text-text-secondary">{{ $totalCount ?? $articles->total() }} bài viết chép chính tả</p>
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
        <input
          name="search"
          type="search"
          value="{{ $filters['search'] ?? '' }}"
          placeholder="Tìm kiếm bài viết..."
          class="w-48 md:w-60 pl-8 pr-8 py-2 bg-app-bg border border-border-light rounded-xl text-xs sm:text-sm text-text-primary placeholder:text-text-disabled focus:bg-white transition-all"
        />
        <svg class="w-4 h-4 text-text-disabled absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        @if(!empty($filters['search']))
          <a href="{{ route('client.articles.library', request()->except(['search', 'page'])) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-text-disabled hover:text-text-primary transition-colors" title="Xóa tìm kiếm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </a>
        @endif
      </div>
    </form>
  </x-slot:headerActions>

  {{-- Mobile search bar --}}
  <div class="sm:hidden mb-4">
    <form method="GET" action="{{ route('client.articles.library') }}" class="relative w-full">
      @foreach($filters as $k => $v)
        @if($k !== 'search')
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
      @endforeach
      <input
        name="search"
        type="search"
        value="{{ $filters['search'] ?? '' }}"
        placeholder="Tìm kiếm bài viết..."
        class="w-full pl-8 pr-8 py-2.5 bg-white border border-border-light rounded-xl text-sm text-text-primary placeholder:text-text-disabled"
      />
      <svg class="w-4 h-4 text-text-disabled absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      @if(!empty($filters['search']))
        <a href="{{ route('client.articles.library', request()->except(['search', 'page'])) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-text-disabled hover:text-text-primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </a>
      @endif
    </form>
  </div>

  {{-- Filter Toolbar: Categories + Access + Sort --}}
  <div class="bg-white rounded-2xl border border-border-light p-3.5 sm:p-4 mb-6 shadow-card space-y-3">
    {{-- Categories pills --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
      <span class="text-xs font-semibold text-text-secondary flex-shrink-0 hidden md:inline mr-1">Chủ đề:</span>
      <a href="{{ route('client.articles.library', request()->except(['category', 'page'])) }}"
         class="inline-flex items-center px-3.5 py-1.5 rounded-xl border text-xs font-semibold transition-all flex-shrink-0 {{ empty($filters['category']) ? 'bg-brand text-white border-brand shadow-sm' : 'bg-app-bg/80 text-text-secondary border-border-light hover:bg-white hover:text-brand hover:border-brand/40' }}">
        Tất cả chủ đề
      </a>
      @foreach($categories as $cat)
        <a href="{{ route('client.articles.library', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}"
           class="inline-flex items-center px-3.5 py-1.5 rounded-xl border text-xs font-semibold transition-all flex-shrink-0 {{ ($filters['category'] ?? '') === $cat->slug ? 'bg-brand text-white border-brand shadow-sm' : 'bg-app-bg/80 text-text-secondary border-border-light hover:bg-white hover:text-brand hover:border-brand/40' }}">
          {{ $cat->name }}
        </a>
      @endforeach
    </div>

    {{-- Bottom bar: Access Segmented Filter + Sort + Reset --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-border-light/60">
      {{-- Access Filter Pills --}}
      <div class="flex items-center gap-1 bg-app-bg p-1 rounded-xl border border-border-light/60">
        <a href="{{ route('client.articles.library', request()->except(['access', 'page'])) }}"
           class="px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ empty($filters['access']) ? 'bg-white text-brand shadow-sm font-bold' : 'text-text-secondary hover:text-text-primary' }}">
          Tất cả gói
        </a>
        <a href="{{ route('client.articles.library', array_merge(request()->except(['access', 'page']), ['access' => 'free'])) }}"
           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ ($filters['access'] ?? '') === 'free' ? 'bg-white text-emerald-600 shadow-sm font-bold' : 'text-text-secondary hover:text-text-primary' }}">
          Miễn phí
        </a>
        <a href="{{ route('client.articles.library', array_merge(request()->except(['access', 'page']), ['access' => 'pro'])) }}"
           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ ($filters['access'] ?? '') === 'pro' ? 'bg-white text-amber-600 shadow-sm font-bold' : 'text-text-secondary hover:text-text-primary' }}">
          <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
          Gói Pro
        </a>
      </div>

      {{-- Right controls: Sort dropdown + Reset --}}
      <div class="flex items-center gap-2 ml-auto">
        <form method="GET" action="{{ route('client.articles.library') }}" class="flex items-center gap-1.5">
          @foreach($filters as $k => $v)
            @if($k !== 'sort' && $k !== 'page')
              <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endif
          @endforeach
          <label for="sortSelect" class="text-xs text-text-secondary font-medium hidden sm:inline">Sắp xếp:</label>
          <select id="sortSelect" name="sort" onchange="this.form.submit()" class="bg-app-bg border border-border-light rounded-xl px-2.5 py-1 text-xs text-text-primary font-medium focus:bg-white cursor-pointer">
            <option value="latest" {{ ($filters['sort'] ?? 'latest') === 'latest' ? 'selected' : '' }}>Mới nhất</option>
            <option value="title_asc" {{ ($filters['sort'] ?? '') === 'title_asc' ? 'selected' : '' }}>Tiêu đề A → Z</option>
            <option value="title_desc" {{ ($filters['sort'] ?? '') === 'title_desc' ? 'selected' : '' }}>Tiêu đề Z → A</option>
          </select>
        </form>

        @if(!empty($filters['category']) || !empty($filters['access']) || !empty($filters['search']) || !empty($filters['sort']))
          <a href="{{ route('client.articles.library') }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl border border-red-200 text-xs font-semibold text-semantic-red bg-red-50/60 hover:bg-red-50 transition-colors" title="Đặt lại bộ lọc">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>Đặt lại</span>
          </a>
        @endif
      </div>
    </div>
  </div>

  {{-- Active filter notification if filtering --}}
  @if(!empty($filters['search']) || !empty($filters['category']) || !empty($filters['access']))
    <div class="flex items-center justify-between gap-2 mb-4 px-1 text-xs text-text-secondary">
      <span>Đang hiển thị <strong class="text-text-primary">{{ $articles->total() }}</strong> bài viết phù hợp bộ lọc</span>
    </div>
  @endif

  {{-- Articles grid --}}
  @if($articles->isEmpty())
    <div class="bg-white rounded-2xl border border-border-light p-10 sm:p-12 text-center shadow-card max-w-lg mx-auto my-6">
      <div class="w-14 h-14 bg-brand-light rounded-2xl flex items-center justify-center mx-auto mb-3 text-brand">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
      </div>
      <h2 class="text-base font-bold text-text-primary mb-1">Không tìm thấy bài viết phù hợp</h2>
      <p class="text-xs text-text-secondary mb-4">Vui lòng thử tìm kiếm bằng từ khóa khác hoặc thiết lập lại bộ lọc.</p>
      <a href="{{ route('client.articles.library') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand text-white text-xs font-semibold rounded-xl hover:bg-brand-dark transition-colors shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        <span>Xem tất cả bài viết</span>
      </a>
    </div>
  @else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 xl:gap-6 items-stretch">
      @foreach($articles as $article)
        @php
          $isPro = $article->is_premium;
          $userIsPro = auth()->user()->isPro();
          $locked = $isPro && !$userIsPro;
          $sentenceCount = $article->sentenceCount();
        @endphp
        <div class="article-card group flex flex-col h-full bg-white rounded-2xl border border-border-light hover:border-brand/40 overflow-hidden shadow-card transition-all duration-200 {{ $locked ? 'bg-gradient-to-b from-white to-amber-50/10' : '' }}">
          {{-- Cover image with ratio 16:9 and object-fit cover center --}}
          <div class="relative w-full aspect-video overflow-hidden bg-slate-100 flex-shrink-0">
            @if($article->image_path)
              <img
                src="{{ $article->imageUrl() }}"
                alt="{{ $article->title }}"
                class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
              />
            @else
              {{-- Branded placeholder --}}
              <div class="w-full h-full bg-gradient-to-br from-brand-light via-white to-brand/10 flex flex-col items-center justify-center text-brand/40 p-4 select-none">
                <div class="w-12 h-12 rounded-xl bg-white/90 shadow-sm border border-brand/20 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
                  </svg>
                </div>
                <span class="text-[11px] font-semibold text-brand/70 tracking-wider uppercase">Dictation Lab</span>
              </div>
            @endif

            {{-- Subtle overlay on hover --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>

            {{-- Pro / Free Pill --}}
            @if($isPro)
              <div class="absolute top-3 right-3 flex items-center gap-1 px-2.5 py-1 bg-white/95 backdrop-blur-md rounded-full shadow-card border border-amber-200/80 text-amber-700">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="text-[11px] font-bold tracking-wide">PRO</span>
              </div>
            @else
              <div class="absolute top-3 right-3 flex items-center gap-1 px-2.5 py-1 bg-white/95 backdrop-blur-md rounded-full shadow-card border border-emerald-200/70 text-brand">
                <span class="text-[11px] font-bold">Miễn phí</span>
              </div>
            @endif

            {{-- Sentence count chip on bottom-left of the image --}}
            <div class="absolute bottom-2.5 left-2.5 flex items-center gap-1 px-2 py-0.5 rounded-lg bg-black/60 backdrop-blur-md text-white text-[11px] font-medium shadow-sm">
              <svg class="w-3 h-3 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
              </svg>
              <span>{{ $sentenceCount }} câu</span>
            </div>
          </div>

          {{-- Card body: flex-1 flex flex-col to ensure uniform heights --}}
          <div class="flex-1 flex flex-col p-4 sm:p-5">
            {{-- Category tags --}}
            <div class="flex items-center gap-1.5 mb-2.5 flex-wrap min-h-[1.5rem]">
              @forelse($article->categories as $cat)
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-brand-light text-brand">
                  {{ $cat->name }}
                </span>
              @empty
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-text-secondary">
                  Bài luyện
                </span>
              @endforelse
            </div>

            {{-- Article Title: min-height 2-line space ensures alignment --}}
            <h3 class="font-bold text-sm sm:text-base text-text-primary leading-snug line-clamp-2 min-h-[2.5rem] group-hover:text-brand transition-colors mb-2">
              {{ $article->title }}
            </h3>

            {{-- Excerpt: flex-1 pushes button strictly to the bottom --}}
            <div class="flex-1 mb-4">
              @if($article->excerpt)
                <p class="text-xs text-text-secondary line-clamp-2 leading-relaxed">{{ $article->excerpt }}</p>
              @else
                <p class="text-xs text-text-disabled italic">Luyện nghe và chép chính tả câu theo ngữ cảnh bài học.</p>
              @endif
            </div>

            {{-- Bottom action button --}}
            <div class="mt-auto pt-3 border-t border-border-light/70">
              @if($locked)
                <a href="{{ route('client.checkout') }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-amber-50/80 border border-amber-200 text-xs font-semibold text-amber-800 rounded-xl hover:bg-amber-100 hover:border-amber-300 transition-all shadow-sm">
                  <svg class="w-3.5 h-3.5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                  </svg>
                  <span>Nâng cấp Pro để mở khóa</span>
                </a>
              @else
                <a href="{{ route('client.learning.dictation', $article) }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-brand text-white text-xs font-semibold rounded-xl hover:bg-brand-dark transition-all shadow-sm hover:shadow">
                  <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span>Bắt đầu chép chính tả</span>
                </a>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
      {{ $articles->withQueryString()->links() }}
    </div>
  @endif
</x-client.layout.dashboard>
