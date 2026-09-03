<x-client.layout.dashboard title="Tổng quan" activePage="dashboard">
  <x-slot:headerContent>
    <div>
      <h1 class="text-lg font-bold text-text-primary">Xin chào, {{ auth()->user()->name }}! 👋</h1>
      <p class="text-xs text-text-secondary">{{ now()->translatedFormat('l, d \\t\\há\\n\\g n, Y') }}</p>
    </div>
  </x-slot:headerContent>

  <x-slot:headerActions>
    @unless(auth()->user()->isPro())
      <a href="{{ route('client.checkout') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-semibold rounded-lg hover:bg-yellow-100 transition-colors">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
        Nâng cấp Pro
      </a>
    @endunless
    <a href="{{ route('client.articles.library') }}" class="px-3 py-1.5 bg-brand text-white text-xs font-semibold rounded-lg hover:bg-brand-dark transition-colors">Bắt đầu luyện</a>
  </x-slot:headerActions>

  @php
    $totalArticles = $stats['total_articles_completed'] ?? 0;
    $totalSentences = $stats['total_sentences'] ?? 0;
    $avgWpm = $stats['avg_wpm'] ?? 0;
    $avgAccuracy = $stats['avg_accuracy'] ?? 0;
    $vocabCount = $stats['total_vocab'] ?? 0;
  @endphp

  {{-- KPI Cards --}}
  <div class="grid grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border border-border-light hover:shadow-float hover:-translate-y-0.5 transition-all">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-7 h-7 bg-brand-light rounded-lg flex items-center justify-center">
          <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <p class="text-xs font-medium text-text-secondary">Bài đã hoàn thành</p>
      </div>
      <p class="text-2xl font-black text-text-primary">{{ number_format($totalArticles) }}</p>
      <p class="text-xs text-text-disabled mt-1">bài viết</p>
    </div>

    <div class="bg-white rounded-xl p-4 border border-border-light hover:shadow-float hover:-translate-y-0.5 transition-all">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
          <svg class="w-3.5 h-3.5 text-semantic-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
        </div>
        <p class="text-xs font-medium text-text-secondary">Câu đã chép</p>
      </div>
      <p class="text-2xl font-black text-text-primary">{{ number_format($totalSentences) }}</p>
      <p class="text-xs text-text-disabled mt-1">câu</p>
    </div>

    <div class="bg-white rounded-xl p-4 border border-border-light hover:shadow-float hover:-translate-y-0.5 transition-all">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-7 h-7 bg-indigo-50 rounded-lg flex items-center justify-center">
          <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
        </div>
        <p class="text-xs font-medium text-text-secondary">WPM trung bình</p>
      </div>
      <p class="text-2xl font-black text-indigo-600">{{ $avgWpm }}</p>
      <p class="text-xs text-text-disabled mt-1">từ/phút</p>
    </div>

    <div class="bg-white rounded-xl p-4 border border-border-light hover:shadow-float hover:-translate-y-0.5 transition-all">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-7 h-7 bg-emerald-50 rounded-lg flex items-center justify-center">
          <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-xs font-medium text-text-secondary">Chính xác TB</p>
      </div>
      <p class="text-2xl font-black text-emerald-600">{{ $avgAccuracy }}%</p>
      <p class="text-xs text-text-disabled mt-1">độ chính xác</p>
    </div>

    <div class="bg-white rounded-xl p-4 border border-border-light hover:shadow-float hover:-translate-y-0.5 transition-all">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-7 h-7 bg-amber-50 rounded-lg flex items-center justify-center">
          <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        </div>
        <p class="text-xs font-medium text-text-secondary">Từ vựng đã lưu</p>
      </div>
      <p class="text-2xl font-black text-amber-600">{{ number_format($vocabCount) }}</p>
      <p class="text-xs text-text-disabled mt-1">từ / cụm từ</p>
    </div>
  </div>

  {{-- WPM Chart --}}
  @if(!empty($weeklyWpm))
    <div class="bg-white rounded-xl border border-border-light p-5 mb-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-text-primary text-sm">Tốc độ gõ 7 ngày qua</h2>
        <span class="text-xs text-text-secondary">WPM theo ngày</span>
      </div>
      <div class="h-48">
        <canvas id="wpmChart"></canvas>
      </div>
    </div>
  @endif

  <div class="grid xl:grid-cols-3 gap-4 mb-6">
    {{-- Resume / Tiếp tục luyện --}}
    @if($resumeList->isNotEmpty())
      <div class="xl:col-span-2 bg-white rounded-xl border border-border-light p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-text-primary text-sm">Tiếp tục bài đang dở</h2>
          <a href="{{ route('client.articles.library') }}" class="text-xs text-brand hover:text-brand-dark transition-colors">Xem tất cả →</a>
        </div>
        <div class="space-y-2">
          @foreach($resumeList->take(4) as $history)
            @php
              $article = $history->article;
              $total = $article->sentenceCount();
              $done = $history->completed_sentences;
              $pct = $total > 0 ? round($done / $total * 100) : 0;
            @endphp
            <a href="{{ route('client.learning.dictation', $history->article_id) }}" class="group flex items-center gap-3 p-3 rounded-xl hover:bg-app-bg transition-colors">
              <div class="w-8 h-8 rounded-lg bg-brand-light flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-text-primary truncate group-hover:text-brand transition-colors">{{ $article->title }}</p>
                <div class="flex items-center gap-2 mt-1">
                  <div class="flex-1 h-1.5 bg-app-bg rounded-full overflow-hidden">
                    <div class="h-full bg-brand rounded-full" style="width: {{ $pct }}%"></div>
                  </div>
                  <span class="text-[10px] text-text-disabled flex-shrink-0">{{ $done }}/{{ $total }} câu</span>
                </div>
              </div>
            </a>
          @endforeach
        </div>
      </div>
    @endif

    {{-- Categories --}}
    @if($categories->isNotEmpty())
      <div class="bg-white rounded-xl border border-border-light p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-text-primary text-sm">Chủ đề</h2>
          <a href="{{ route('client.articles.library') }}" class="text-xs text-brand hover:text-brand-dark transition-colors">Khám phá →</a>
        </div>
        <div class="space-y-2">
          @foreach($categories as $cat)
            <a href="{{ route('client.articles.library') }}?category={{ $cat->slug }}" class="flex items-center justify-between p-2.5 rounded-lg hover:bg-app-bg transition-colors group">
              <span class="text-sm text-text-secondary group-hover:text-text-primary transition-colors">{{ $cat->name }}</span>
              <span class="text-xs font-semibold text-text-disabled group-hover:text-brand transition-colors">{{ $cat->articles_count }}</span>
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>

  {{-- Recent Activity + Recommended --}}
  <div class="grid xl:grid-cols-2 gap-4 mb-6">
    {{-- Recent Activity --}}
    <div class="bg-white rounded-xl border border-border-light p-5">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-text-primary text-sm">Hoạt động gần đây</h2>
      </div>
      @forelse($recentActivity->take(6) as $activity)
        @php
          $accuracyText = rtrim(rtrim(number_format((float)($activity['accuracy'] ?? 0), 1), '0'), '.');
        @endphp
        <div class="flex items-start gap-3 {{ !$loop->last ? 'mb-3' : '' }}">
          <div class="w-8 h-8 rounded-lg bg-brand-light flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-text-primary truncate">{{ optional($activity['article'])->title ?? 'Bài viết' }}</p>
            <p class="text-xs text-text-secondary mt-0.5">
              {{ $activity['wpm'] ?? 0 }} WPM · {{ $accuracyText }}% · {{ $activity['completed_sentences'] ?? 0 }} câu
              @if($activity['created_at'])
                <span class="text-text-disabled">· {{ $activity['created_at']->diffForHumans() }}</span>
              @endif
            </p>
          </div>
          <a href="{{ route('client.learning.dictation', $activity['article_id']) }}" class="text-[11px] font-semibold text-brand hover:text-brand-dark transition-colors flex-shrink-0 mt-0.5">Tiếp →</a>
        </div>
      @empty
        <p class="text-xs text-text-disabled text-center py-6">Chưa có hoạt động nào. Hãy bắt đầu luyện!</p>
      @endforelse
      <a href="{{ route('client.articles.library') }}" class="mt-4 block text-center text-xs text-brand font-medium hover:text-brand-dark transition-colors">Xem thư viện bài viết →</a>
    </div>

    {{-- Recommended Articles --}}
    @if($recommendedArticles->isNotEmpty())
      <div class="bg-white rounded-xl border border-border-light p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-text-primary text-sm">Bài viết gợi ý</h2>
          <a href="{{ route('client.articles.library') }}" class="text-xs text-brand hover:text-brand-dark transition-colors">Xem tất cả →</a>
        </div>
        <div class="space-y-2.5">
          @foreach($recommendedArticles as $article)
            <a href="{{ route('client.learning.dictation', $article->id) }}" class="flex items-start gap-3 p-3 rounded-xl border border-border-light hover:border-brand transition-colors group block">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5 mb-1">
                  <span class="text-[10px] px-2 py-0.5 {{ $article->is_premium ? 'bg-yellow-50 text-yellow-700' : 'bg-green-50 text-brand' }} font-semibold rounded-full">
                    {{ $article->is_premium ? 'Pro' : 'Miễn phí' }}
                  </span>
                  @foreach($article->categories as $cat)
                    <span class="text-[10px] text-text-disabled">{{ $cat->name }}</span>
                  @endforeach
                </div>
                <p class="text-sm font-semibold text-text-primary group-hover:text-brand transition-colors truncate">{{ $article->title }}</p>
                <p class="text-xs text-text-secondary mt-0.5">{{ $article->sentenceCount() }} câu</p>
              </div>
              <svg class="w-4 h-4 text-text-disabled group-hover:text-brand transition-colors flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          @endforeach
        </div>
      </div>
    @else
      {{-- Upsell for free users when no recommendations --}}
      @unless(auth()->user()->isPro())
        <div class="bg-white rounded-xl border border-border-light p-5 flex flex-col justify-center items-center text-center">
          <div class="w-12 h-12 bg-brand-light rounded-2xl flex items-center justify-center mb-3">
            <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          </div>
          <p class="text-sm font-semibold text-text-primary mb-1">Khám phá thêm bài viết</p>
          <p class="text-xs text-text-secondary mb-4">Nâng cấp Pro để luyện tập không giới hạn với kho bài phong phú.</p>
          <a href="{{ route('client.checkout') }}" class="px-4 py-2 bg-brand text-white text-xs font-bold rounded-lg hover:bg-brand-dark transition-colors">Nâng cấp Pro →</a>
        </div>
      @endunless
    @endif
  </div>

  @unless(auth()->user()->isPro())
    <div class="bg-gradient-to-r from-brand to-brand-dark rounded-xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="text-white">
        <p class="font-bold text-sm flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
          Mở khóa toàn bộ Dictation Lab với Pro
        </p>
        <p class="text-xs opacity-80 mt-1">AI giải thích câu, lưu từ vựng không giới hạn, truy cập toàn bộ kho bài viết.</p>
      </div>
      <a href="{{ route('client.checkout') }}" class="shrink-0 px-4 py-2 bg-white text-brand text-sm font-bold rounded-lg hover:bg-brand-light transition-colors">Nâng cấp Pro →</a>
    </div>
  @endunless

  @if(!empty($weeklyWpm))
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
      (function () {
        const data = @json($weeklyWpm);
        const labels = data.map(d => d.label);
        const wpmValues = data.map(d => d.wpm);
        const hasData = wpmValues.some(v => v > 0);
        if (!hasData) return;
        const ctx = document.getElementById('wpmChart');
        if (!ctx) return;
        new Chart(ctx, {
          type: 'line',
          data: {
            labels,
            datasets: [{
              label: 'WPM',
              data: wpmValues,
              borderColor: '#11A683',
              backgroundColor: 'rgba(17,166,131,0.12)',
              tension: 0.35,
              fill: true,
              pointBackgroundColor: '#11A683',
              pointRadius: 4,
              pointHoverRadius: 5,
            }],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              tooltip: { callbacks: { label: ctx => ctx.parsed.y + ' WPM' } },
            },
            scales: {
              y: { beginAtZero: true, ticks: { stepSize: 10 } },
              x: { grid: { display: false } },
            },
          },
        });
      })();
    </script>
  @endif
</x-client.layout.dashboard>
