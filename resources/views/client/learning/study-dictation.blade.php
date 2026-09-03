<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dictation Lab — {{ $article->title }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'app-bg':'#F9F9FA','surface':'#FFFFFF','border-light':'#E1E4E8',
            'text-primary':'#0E101A','text-secondary':'#6D758D','text-disabled':'#B9BDC5',
            'brand':'#11A683','brand-dark':'#0E8A6D','brand-light':'#E8F8F3',
            'semantic-red':'#FF5E5E','semantic-blue':'#007AFF','semantic-green':'#11A683',
            'semantic-purple':'#8F00FF','semantic-yellow':'#FFD500',
          },
          fontFamily: {
            sans: ['Inter','-apple-system','BlinkMacSystemFont','Segoe UI','Roboto','sans-serif'],
            mono: ['JetBrains Mono','Fira Code','Consolas','monospace'],
          },
          boxShadow: {
            'card':'0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03)',
            'float':'0 4px 6px rgba(0,0,0,0.05), 0 10px 15px rgba(0,0,0,0.1)',
            'tooltip':'0 8px 24px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08)',
          },
        },
      },
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <style>
    html,body{height:100%;overflow:hidden;}
    body{font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#0E101A;background:#F9F9FA;-webkit-font-smoothing:antialiased;}
    :root{
      --text-ui:14px;
      --text-body:16px;
      --text-sub:15px;
      --text-meaning:22px;
      --text-typing:22px;
    }
    @media(max-width:1023px){
      :root{--text-meaning:20px;--text-typing:20px;--text-body:15px;--text-sub:14px;}
    }
    #typing-engine{position:relative;font-family:'JetBrains Mono','Fira Code',monospace;font-size:var(--text-typing);line-height:1.6;letter-spacing:0.02em;cursor:text;min-height:80px;user-select:none;-webkit-user-select:none;word-break:break-word;}
    #typing-engine:focus{outline:none;}
    #currentMeaning{font-size:var(--text-meaning);line-height:1.45;}
    .panel-label{font-size:var(--text-ui);letter-spacing:0.04em;}
    .panel-meta{font-size:var(--text-ui);}
    .sentence-en{font-size:var(--text-body);user-select:text;-webkit-user-select:text;cursor:text;}
    .sentence-vi{font-size:var(--text-sub);color:#6D758D;line-height:1.55;}
    .translation-item{padding:14px 16px;border-radius:0;border:1px solid transparent;border-bottom:1px solid #E1E4E8;transition:background 0.15s,border-color 0.15s,opacity 0.15s;}
    #translationList,#typing-wrapper{overscroll-behavior:contain;-webkit-overflow-scrolling:touch;}
    @media(max-width:1023px){
      body.translation-open #dictationPanel{flex:3 1 0;}
      body.translation-open #translationPanel{flex:2 1 0;}
    }
    .translation-item.is-current{border-color:#11A683;background:rgba(232,248,243,0.6);border-left-width:4px;}
    .translation-item.is-done .sentence-en{color:#11A683;}
    .translation-item:not(.is-current):not(.is-done){opacity:0.55;}
    .char-ghost{color:#D1D5DB;transition:color 0.05s ease;}
    .char-correct{color:#11A683;animation:char-correct-flash 0.4s ease forwards;}
    .char-wrong{color:#FF5E5E;background:rgba(255,94,94,0.1);border-bottom:2px solid rgba(255,94,94,0.6);border-radius:2px;animation:char-wrong-flash 0.3s ease;}
    .char-cursor{position:relative;}
    .char-cursor::before{content:'';position:absolute;left:-1px;top:2px;width:2px;height:1.2em;background-color:#11A683;animation:cursor-blink 1s step-end infinite;border-radius:1px;}
    @keyframes cursor-blink{0%,100%{opacity:1;}50%{opacity:0;}}
    @keyframes char-correct-flash{0%{background:rgba(17,166,131,0.25);color:#11A683;}100%{background:transparent;color:#11A683;}}
    @keyframes char-wrong-flash{0%{background:rgba(255,94,94,0.35);}100%{background:rgba(255,94,94,0.1);}}
    .progress-bar-fill{transition:width 0.4s cubic-bezier(0.4,0,0.2,1);}
    .modal-overlay{display:none;opacity:0;transition:opacity 0.3s ease;}
    .modal-overlay.show{display:flex;opacity:1;}
    .modal-content{transform:scale(0.95) translateY(10px);transition:transform 0.3s cubic-bezier(0.4,0,0.2,1);}
    .modal-overlay.show .modal-content{transform:scale(1) translateY(0);}
    #vocab-tooltip{transform:translateX(-50%) translateY(-100%);}
    @keyframes vocab-pop{0%{opacity:0;transform:translateX(-50%) translateY(-90%);}100%{opacity:1;transform:translateX(-50%) translateY(-100%);}}
    #vocab-tooltip:not(.hidden){animation:vocab-pop 0.15s ease;}
    @keyframes sentence-slide-in{0%{opacity:0;transform:translateY(6px);}100%{opacity:1;transform:translateY(0);}}
    ::-webkit-scrollbar{width:5px;}::-webkit-scrollbar-track{background:transparent;}::-webkit-scrollbar-thumb{background:#D1D5DB;border-radius:3px;}::-webkit-scrollbar-thumb:hover{background:#9CA3AF;}
    @media(prefers-reduced-motion:reduce){.char-cursor::before{animation:none;opacity:1;}.char-correct,.char-wrong{animation:none;}.progress-bar-fill{transition:none;}.modal-overlay,.modal-content{transition:none;}#vocab-tooltip:not(.hidden){animation:none;}}
  </style>
</head>
<body class="bg-app-bg h-dvh max-h-dvh overflow-hidden flex flex-col">
  @php
    $blocks = $article->contentBlocks();
    $sentences = $article->sentences();
    $sentenceTotal = count($sentences);
  @endphp

  {{-- TOP BAR --}}
  <header class="bg-white border-b border-border-light flex-shrink-0 z-40" style="backdrop-filter:blur(12px);background:rgba(255,255,255,0.92);">
    <div class="w-full px-5 lg:px-8 flex items-center justify-between h-14 gap-4">
      <div class="flex items-center gap-3 min-w-0">
        <a href="{{ route('client.articles.library') }}" class="flex items-center justify-center w-8 h-8 rounded-lg text-text-disabled hover:text-text-primary hover:bg-app-bg transition-all flex-shrink-0" title="Quay lại thư viện">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="min-w-0">
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-slate-950 text-white text-[11px] font-brand font-extrabold rounded-md flex-shrink-0 shadow-sm">
              <span class="flex items-center gap-0.5">
                <span class="w-0.5 h-1.5 bg-emerald-400 rounded-full"></span>
                <span class="w-0.5 h-2.5 bg-emerald-400 rounded-full"></span>
                <span class="w-0.5 h-1 bg-emerald-400 rounded-full"></span>
              </span>
              Dictation<span class="text-emerald-400 font-bold">Lab</span>
            </span>
            @foreach($article->categories as $cat)
              <span class="inline-flex items-center px-2 py-0.5 bg-blue-50 text-semantic-blue text-xs font-semibold rounded-full flex-shrink-0">{{ $cat->name }}</span>
            @endforeach
          </div>
          <p class="text-base font-bold text-text-primary leading-tight truncate mt-0.5">{{ $article->title }}</p>
        </div>
      </div>

      <div class="hidden sm:flex items-center gap-1 flex-shrink-0">
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-app-bg transition-colors">
          <svg class="w-4 h-4 text-text-disabled" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
          <div class="text-center"><p class="text-base font-black text-text-primary leading-none" id="wpmDisplay">0</p><p class="text-xs text-text-disabled leading-tight mt-0.5">WPM</p></div>
        </div>
        <div class="w-px h-6 bg-border-light"></div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-app-bg transition-colors">
          <svg class="w-4 h-4 text-text-disabled" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <div class="text-center"><p class="text-base font-black text-brand leading-none" id="accDisplay">100%</p><p class="text-xs text-text-disabled leading-tight mt-0.5">Chính xác</p></div>
        </div>
        <div class="w-px h-6 bg-border-light"></div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-app-bg transition-colors">
          <svg class="w-4 h-4 text-text-disabled" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
          <div class="text-center"><p class="text-base font-black text-text-primary leading-none" id="progressDisplay">0/{{ $sentenceTotal }}</p><p class="text-xs text-text-disabled leading-tight mt-0.5">Câu</p></div>
        </div>
      </div>

      <div class="flex items-center gap-2 flex-shrink-0">
        <button id="soundToggle" onclick="toggleSound()" title="Bật/tắt âm thanh" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-border-light text-text-disabled hover:text-brand hover:border-brand transition-all cursor-pointer text-sm">
          🔇
        </button>
        <button onclick="resetSession()" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-border-light text-sm font-medium text-text-secondary rounded-lg hover:bg-app-bg hover:border-brand hover:text-brand transition-all cursor-pointer">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
          Đặt lại
        </button>
      </div>
    </div>
    <div class="h-1 bg-app-bg"><div class="progress-bar-fill h-full bg-gradient-to-r from-brand to-emerald-400 rounded-r-full" id="progressBar" style="width:0%"></div></div>
  </header>

  {{-- MAIN: full-width split — trái: chép chính tả | phải: bản dịch --}}
  <main class="flex-1 min-h-0 overflow-hidden w-full flex flex-col lg:grid lg:grid-cols-2 lg:gap-0 gap-2 px-0 py-2 lg:py-0">

    {{-- Dictation Panel (trái desktop) --}}
    <section id="dictationPanel" class="flex flex-col min-h-0 flex-1 order-1 lg:order-1 lg:h-full bg-white overflow-hidden mx-3 lg:mx-0 rounded-2xl lg:rounded-none border border-border-light lg:border-0 lg:border-r lg:border-border-light shadow-card lg:shadow-none">
      <div class="flex items-center justify-between px-5 lg:px-8 py-3 border-b border-border-light bg-app-bg/50 flex-shrink-0">
        <span class="panel-meta font-bold text-text-secondary">Câu <span id="sentenceLabel">1</span> / {{ $sentenceTotal }}</span>
        <span class="panel-meta text-text-disabled"><span id="charCount">0</span> / <span id="totalChars">0</span> ký tự</span>
      </div>

      <div class="px-5 lg:px-8 pt-4 pb-3 flex-shrink-0 border-b border-border-light/60 max-h-[28vh] overflow-y-auto overscroll-behavior-contain">
        <div id="section-heading" class="hidden mb-3">
          <p class="panel-label font-bold text-text-disabled uppercase mb-1">Phần đang học</p>
          <p id="section-heading-text" class="font-bold text-text-primary text-xl"></p>
        </div>
        <p class="panel-label font-bold text-text-disabled uppercase mb-2 flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
          Nghĩa câu đang chép
        </p>
        <p id="currentMeaning" class="font-semibold text-text-primary">Đang tải...</p>
      </div>

      <div id="typing-wrapper" class="flex-1 min-h-0 px-5 lg:px-8 py-4 overflow-y-auto">
        <div id="typing-engine" tabindex="0" autofocus></div>
      </div>

      <div class="flex items-center justify-between px-5 lg:px-8 py-3 border-t border-border-light bg-app-bg/50 flex-shrink-0 gap-2 flex-wrap">
        <div class="flex items-center gap-3">
          <span class="flex items-center gap-1.5 panel-meta text-text-secondary"><span class="w-2 h-2 rounded-full bg-brand"></span> Đúng</span>
          <span class="flex items-center gap-1.5 panel-meta text-text-secondary"><span class="w-2 h-2 rounded-full bg-semantic-red"></span> Sai</span>
          <span class="flex items-center gap-1.5 panel-meta text-text-secondary"><span class="w-2 h-2 rounded-full bg-text-disabled"></span> Chưa gõ</span>
        </div>
        <span class="panel-meta text-text-disabled hidden sm:inline">Gõ sai hiện <span class="text-semantic-red font-medium">đỏ</span> — <kbd class="px-1.5 py-0.5 bg-white border border-border-light rounded text-xs font-mono">Backspace</kbd> để sửa</span>
      </div>
    </section>

    {{-- Mobile translation toggle --}}
    <button type="button" id="toggleTranslation" aria-expanded="false"
      class="lg:hidden order-2 mx-3 flex-shrink-0 inline-flex items-center justify-center gap-1.5 w-[calc(100%-1.5rem)] py-2 panel-meta font-medium text-text-secondary bg-white border border-border-light rounded-xl hover:border-brand hover:text-brand transition-all cursor-pointer">
      📖 Hiện bản dịch ({{ $sentenceTotal }} câu)
    </button>

    {{-- Translation Panel (phải desktop) --}}
    <aside id="translationPanel"
      class="hidden lg:flex flex-col min-h-0 order-3 lg:order-2 lg:h-full bg-white overflow-hidden mx-3 lg:mx-0 rounded-2xl lg:rounded-none border lg:border-0">
      <div class="px-5 lg:px-8 py-3 border-b border-border-light bg-app-bg/50 flex-shrink-0">
        <h3 class="panel-label font-bold text-text-secondary uppercase">Bản dịch</h3>
        <p class="panel-meta text-text-disabled mt-1">Chọn từ trong câu đã xong để lưu từ vựng</p>
      </div>
      <div id="translationList" class="flex-1 min-h-0 overflow-y-auto"></div>
    </aside>
  </main>

  {{-- Floating vocab tooltip --}}
  <div id="vocab-tooltip" class="hidden fixed z-50 pointer-events-auto">
    <div class="bg-white rounded-xl shadow-tooltip border border-border-light px-4 py-2.5 flex items-center gap-2 whitespace-nowrap">
      <button id="vocab-save-btn" onclick="saveVocab()" class="text-sm font-medium text-brand hover:text-brand-dark transition-colors cursor-pointer">
        📌 Thêm vào sổ từ vựng
      </button>
    </div>
  </div>

  {{-- COMPLETION MODAL --}}
  <div id="completionModal" class="modal-overlay fixed inset-0 z-50 bg-black/40 items-center justify-center p-4">
    <div class="modal-content bg-white rounded-2xl w-full max-w-md shadow-float overflow-hidden">
      <div class="relative bg-gradient-to-br from-brand to-emerald-500 px-6 py-8 text-center overflow-hidden">
        <div class="absolute inset-0 opacity-10">
          <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
          <div class="absolute bottom-0 left-0 w-24 h-24 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
        </div>
        <div class="relative">
          <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <h2 class="text-xl font-bold text-white">Xuất sắc! Hoàn thành rồi!</h2>
          <p class="text-sm text-white/80 mt-1">Bạn đã chép xong toàn bộ bài viết</p>
        </div>
      </div>
      <div class="px-6 py-5 grid grid-cols-4 gap-3 border-b border-border-light">
        <div class="text-center"><p class="text-2xl font-black text-text-primary" id="modalWpm">0</p><p class="text-xs text-text-disabled mt-0.5">WPM</p></div>
        <div class="text-center"><p class="text-2xl font-black text-brand" id="modalAcc">100%</p><p class="text-xs text-text-disabled mt-0.5">Chính xác</p></div>
        <div class="text-center"><p class="text-2xl font-black text-text-primary" id="modalTime">0:00</p><p class="text-xs text-text-disabled mt-0.5">Thời gian</p></div>
        <div class="text-center"><p class="text-2xl font-black text-semantic-purple" id="modalSentences">0</p><p class="text-xs text-text-disabled mt-0.5">Câu</p></div>
      </div>
      <div class="px-6 py-5 space-y-2.5">
        <a href="{{ route('client.articles.library') }}" class="flex items-center justify-center gap-2 w-full py-3 bg-brand text-white text-sm font-semibold rounded-xl hover:bg-brand-dark transition-colors cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          Về thư viện bài viết
        </a>
        <button onclick="resetSession()" class="w-full py-2.5 border border-border-light text-sm font-medium text-text-secondary rounded-xl hover:bg-app-bg hover:border-brand hover:text-brand transition-all cursor-pointer">Chép lại từ đầu</button>
      </div>
    </div>
  </div>

  @php $isPro = auth()->user()->isPro(); @endphp

  <script>
    const BLOCKS = @json($blocks);
    const SENTENCES = @json($sentences);
    const ARTICLE_ID = {{ $article->id }};
    const SAVE_URL = @json(route('client.learning.dictation.save'));
    const EXPLAIN_URL = @json(route('client.learning.dictation.explain'));
    const VOCAB_URL = @json(route('client.vocabulary.store'));
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const IS_PRO = {{ $isPro ? 'true' : 'false' }};
    const TOTAL = SENTENCES.length;

    const CHAR_EQUIVALENCE = {
      '\u2014':'-','\u2013':'-','\u2018':"'",'\u2019':"'",'\u201C':'"','\u201D':'"','\u2026':'.',
    };

    function matchesExpected(typed, expected) {
      if (typed === expected) return true;
      const equiv = CHAR_EQUIVALENCE[expected];
      return equiv !== undefined && typed === equiv;
    }

    let currentIdx = 0;
    let cursorPos = 0;
    let charStates = [];
    let totalCorrect = 0;
    let totalKeystrokes = 0;
    let startTime = null;
    let isCompleted = false;
    let soundEnabled = false;
    let audioCtx = null;

    // --- Audio ---
    function playTing() {
      if (!soundEnabled) return;
      try {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.frequency.value = 880;
        osc.type = 'sine';
        gain.gain.setValueAtTime(0.25, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.3);
        osc.start(audioCtx.currentTime);
        osc.stop(audioCtx.currentTime + 0.35);
      } catch (_) {}
    }

    function toggleSound() {
      soundEnabled = !soundEnabled;
      const btn = document.getElementById('soundToggle');
      btn.textContent = soundEnabled ? '🔔' : '🔇';
      btn.title = soundEnabled ? 'Tắt âm thanh' : 'Bật âm thanh';
    }

    // --- Core helpers ---
    function currentSentence() { return SENTENCES[currentIdx] ?? null; }

    function escapeHtml(str) {
      const map = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};
      return str.replace(/[&<>"']/g, c => map[c]);
    }

    function prefersReducedMotion() {
      return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function scrollIntoContainer(element, container, { block = 'nearest', margin = 16 } = {}) {
      if (!element || !container) return;
      const elRect = element.getBoundingClientRect();
      const containerRect = container.getBoundingClientRect();
      let delta = 0;

      if (block === 'center') {
        const targetTop = elRect.top - containerRect.top - (containerRect.height / 2) + (elRect.height / 2);
        delta = targetTop;
      } else {
        if (elRect.top < containerRect.top + margin) {
          delta = elRect.top - containerRect.top - margin;
        } else if (elRect.bottom > containerRect.bottom - margin) {
          delta = elRect.bottom - containerRect.bottom + margin;
        }
      }

      if (delta !== 0) {
        container.scrollBy({
          top: delta,
          behavior: prefersReducedMotion() ? 'auto' : 'smooth',
        });
      }
    }

    function scrollTranslationToCurrent() {
      const list = document.getElementById('translationList');
      const cur = list?.querySelector('.translation-item.is-current');
      if (!list || !cur) return;
      scrollIntoContainer(cur, list, { block: 'center', margin: 12 });
    }

    function scrollTypingCursorIntoView() {
      const wrapper = document.getElementById('typing-wrapper');
      const cursor = document.querySelector('#typing-engine .char-cursor');
      if (!wrapper || !cursor) return;
      scrollIntoContainer(cursor, wrapper, { block: 'nearest', margin: 24 });
    }

    // --- Render ---
    function init() {
      if (!TOTAL) {
        document.getElementById('currentMeaning').textContent = 'Bài viết này chưa có nội dung.';
        document.getElementById('typing-engine').innerHTML = '<span class="text-text-disabled text-sm">Không có câu nào để chép.</span>';
        return;
      }
      buildTranslationList();
      renderCurrentSentence();
      updateStats();
      updateProgress();
      setupVocabHighlight();
      setTimeout(() => document.getElementById('typing-engine').focus(), 100);
    }

    function getActiveHeading(sentenceIdx) {
      let heading = null;
      let si = 0;
      for (const block of BLOCKS) {
        if (block.type === 'heading') heading = block;
        if (block.type === 'sentence') {
          if (si === sentenceIdx) return heading;
          si++;
        }
      }
      return heading;
    }

    function updateSectionHeading() {
      const heading = getActiveHeading(currentIdx);
      const wrap = document.getElementById('section-heading');
      const textEl = document.getElementById('section-heading-text');
      if (!wrap || !textEl) return;
      if (!heading || !heading.text) {
        wrap.classList.add('hidden');
        return;
      }
      wrap.classList.remove('hidden');
      const sizeClass = heading.level === 1 ? 'text-2xl' : heading.level === 2 ? 'text-xl' : 'text-lg';
      textEl.className = `font-bold text-text-primary ${sizeClass}`;
      textEl.textContent = heading.text;
    }

    function buildTranslationList() {
      const list = document.getElementById('translationList');
      if (!list) return;
      let sentenceIndex = 0;
      list.innerHTML = BLOCKS.map((block) => {
        if (block.type === 'heading') {
          const headingClass = block.level === 1 ? 'text-lg font-bold' : block.level === 2 ? 'text-base font-semibold' : 'text-sm font-semibold';
          return `
            <div class="translation-heading px-5 py-3 bg-app-bg/50 border-b border-border-light">
              <p class="${headingClass} text-text-primary">${escapeHtml(block.text || '')}</p>
            </div>`;
        }
        if (block.type !== 'sentence') return '';
        const i = sentenceIndex++;
        const explainHtml = IS_PRO
          ? `<div class="explain-wrap hidden mt-2">
               <button type="button" class="explain-btn inline-flex items-center gap-1 panel-meta text-semantic-purple hover:text-purple-700 font-medium transition-colors cursor-pointer" onclick="explainSentence(this, ${i})">💡 Giải thích</button>
               <div class="explain-result hidden mt-2 panel-meta text-text-secondary bg-purple-50 rounded-lg p-3 leading-relaxed whitespace-pre-line"></div>
             </div>`
          : '';
        return `
          <div class="translation-item" data-sentence-index="${i}">
            <div class="flex items-start gap-2.5">
              <span class="done-marker hidden text-brand panel-meta flex-shrink-0 mt-0.5" aria-hidden="true">✓</span>
              <div class="min-w-0 flex-1">
                <p class="sentence-en font-mono text-text-primary leading-relaxed break-words">${escapeHtml(block.en || '')}</p>
                <p class="sentence-vi mt-1.5 leading-relaxed">${escapeHtml(block.vi || '')}</p>
                ${explainHtml}
              </div>
            </div>
          </div>`;
      }).join('');
      highlightCurrentInPanel();
    }

    function highlightCurrentInPanel() {
      document.querySelectorAll('.translation-item').forEach(el => {
        const idx = parseInt(el.getAttribute('data-sentence-index'), 10);
        el.classList.toggle('is-current', idx === currentIdx);
        el.classList.toggle('is-done', idx < currentIdx);
        const marker = el.querySelector('.done-marker');
        const explainWrap = el.querySelector('.explain-wrap');
        if (marker) marker.classList.toggle('hidden', idx >= currentIdx);
        if (explainWrap) explainWrap.classList.toggle('hidden', idx >= currentIdx);
      });
      requestAnimationFrame(scrollTranslationToCurrent);
    }

    function renderCurrentSentence() {
      const sent = currentSentence();
      if (!sent) return;
      document.getElementById('currentMeaning').textContent = sent.vi || '(Không có nghĩa tiếng Việt)';
      renderChars(sent.en);
      document.getElementById('totalChars').textContent = sent.en.length;
      document.getElementById('charCount').textContent = cursorPos;
      highlightCurrentInPanel();
      updateSectionHeading();
    }

    function renderChars(text) {
      const engine = document.getElementById('typing-engine');
      let html = '';
      for (let i = 0; i < text.length; i++) {
        const ch = text[i];
        let css = 'char-ghost', extra = '';
        if (i < cursorPos) css = charStates[i] === 'wrong' ? 'char-wrong' : 'char-correct';
        else if (i === cursorPos) extra = ' char-cursor';
        if (ch === '\n') {
          html += i === cursorPos
            ? `<span class="char-ghost${extra}" data-idx="${i}">↵</span><br>`
            : (i < cursorPos ? `<br data-idx="${i}">` : `<span class="char-ghost" data-idx="${i}">↵</span><br>`);
        } else {
          html += `<span class="${css}${extra}" data-idx="${i}">${escapeHtml(ch)}</span>`;
        }
      }
      engine.innerHTML = html;
      requestAnimationFrame(scrollTypingCursorIntoView);
    }

    // --- Input handling ---
    function handleKeydown(e) {
      if (isCompleted || currentIdx >= TOTAL) return;
      const key = e.key;
      const sent = currentSentence();
      if (!sent) return;
      const text = sent.en;

      if (!startTime && key.length === 1) startTime = Date.now();

      if (key === 'Backspace') {
        e.preventDefault();
        if (cursorPos > 0) {
          charStates[cursorPos] = null;
          cursorPos--;
          charStates[cursorPos] = null;
          renderChars(text);
          document.getElementById('charCount').textContent = cursorPos;
          updateStats();
        }
        return;
      }

      if (key === 'Enter' || (key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey)) {
        e.preventDefault();
        if (cursorPos >= text.length) return;
        const expected = text[cursorPos];
        const typed = key === 'Enter' ? '\n' : key;
        totalKeystrokes++;
        if (matchesExpected(typed, expected)) {
          totalCorrect++;
          charStates[cursorPos] = 'correct';
        } else {
          charStates[cursorPos] = 'wrong';
        }
        cursorPos++;
        renderChars(text);
        document.getElementById('charCount').textContent = cursorPos;
        updateStats();
        if (cursorPos >= text.length) onSentenceComplete(sent, currentIdx);
      }
    }

    // --- Sentence completion ---
    function onSentenceComplete(sent, idx) {
      playTing();
      currentIdx++;
      cursorPos = 0;
      charStates = [];
      highlightCurrentInPanel();
      updateProgress();
      updateSectionHeading();
      if (currentIdx >= TOTAL) {
        completeSession();
      } else {
        renderCurrentSentence();
        document.getElementById('typing-engine').focus();
      }
    }

    // --- Explain (Pro) ---
    function explainSentence(btn, idx) {
      const sent = SENTENCES[idx];
      if (!sent) return;
      const item = btn.closest('.translation-item');
      const resultDiv = item.querySelector('.explain-result');
      btn.disabled = true;
      btn.textContent = '⏳ Đang giải thích...';

      fetch(EXPLAIN_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ sentence_en: sent.en, sentence_vi: sent.vi }),
      })
      .then(r => r.json())
      .then(data => {
        btn.disabled = false;
        btn.innerHTML = '💡 Giải thích';
        if (data.success && data.explanation) {
          resultDiv.textContent = data.explanation;
          resultDiv.classList.remove('hidden');
        } else {
          resultDiv.textContent = data.message || 'Có lỗi xảy ra.';
          resultDiv.classList.remove('hidden');
        }
      })
      .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '💡 Giải thích';
      });
    }

    // --- Stats ---
    function updateStats() {
      if (startTime) {
        const elapsed = (Date.now() - startTime) / 60000;
        let wordCount = 0;
        for (let i = 0; i < currentIdx; i++) {
          wordCount += (SENTENCES[i].en || '').trim().split(/\s+/).filter(w => w.length > 0).length;
        }
        const partial = (currentSentence()?.en ?? '').substring(0, cursorPos);
        wordCount += partial.trim().split(/\s+/).filter(w => w.length > 0).length;
        const wpm = elapsed > 0 ? Math.round(wordCount / elapsed) : 0;
        document.getElementById('wpmDisplay').textContent = wpm;
      }
      const acc = totalKeystrokes > 0 ? Math.round((totalCorrect / totalKeystrokes) * 100) : 100;
      document.getElementById('accDisplay').textContent = acc + '%';
    }

    function updateProgress() {
      const done = currentIdx;
      const pct = TOTAL > 0 ? Math.round((done / TOTAL) * 100) : 0;
      document.getElementById('progressBar').style.width = pct + '%';
      document.getElementById('progressDisplay').textContent = done + '/' + TOTAL;
      const label = document.getElementById('sentenceLabel');
      if (label) label.textContent = Math.min(currentIdx + 1, TOTAL);
    }

    // --- Completion ---
    function completeSession() {
      isCompleted = true;
      const elapsed = startTime ? (Date.now() - startTime) : 0;
      const m = Math.floor(elapsed / 60000);
      const s = Math.floor((elapsed % 60000) / 1000);
      const ts = m + ':' + s.toString().padStart(2, '0');
      const acc = totalKeystrokes > 0 ? Math.round((totalCorrect / totalKeystrokes) * 100) : 100;
      let wordCount = 0;
      for (const sent of SENTENCES) wordCount += (sent.en || '').trim().split(/\s+/).filter(w => w.length > 0).length;
      const wpm = (elapsed / 60000) > 0 ? Math.round(wordCount / (elapsed / 60000)) : 0;

      document.getElementById('modalWpm').textContent = wpm;
      document.getElementById('modalAcc').textContent = acc + '%';
      document.getElementById('modalTime').textContent = ts;
      document.getElementById('modalSentences').textContent = TOTAL;
      document.getElementById('progressBar').style.width = '100%';
      document.getElementById('progressDisplay').textContent = TOTAL + '/' + TOTAL;

      fetch(SAVE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ article_id: ARTICLE_ID, wpm, accuracy: acc, completed_sentences: TOTAL }),
      }).catch(() => {});

      setTimeout(() => document.getElementById('completionModal').classList.add('show'), 600);
    }

    function resetSession() {
      currentIdx = 0;
      cursorPos = 0;
      charStates = [];
      totalCorrect = 0;
      totalKeystrokes = 0;
      startTime = null;
      isCompleted = false;
      document.querySelectorAll('.explain-result').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
      });
      document.querySelectorAll('.explain-btn').forEach(btn => {
        btn.disabled = false;
        btn.innerHTML = '💡 Giải thích';
      });
      document.getElementById('completionModal').classList.remove('show');
      renderCurrentSentence();
      updateStats();
      updateProgress();
      updateSectionHeading();
      document.getElementById('typing-engine').focus();
    }

    // --- Vocab highlight-to-save ---
    let pendingVocab = null;
    const vocabTooltip = document.getElementById('vocab-tooltip');
    const vocabSaveBtn = document.getElementById('vocab-save-btn');

    function setupVocabHighlight() {
      const section = document.getElementById('translationList');
      if (!section) return;

      section.addEventListener('mouseup', function (e) {
        const sel = window.getSelection();
        const text = sel?.toString().trim();
        if (!text || text.length < 2 || text.length > 150) {
          hideVocabTooltip();
          return;
        }
        const enEl = e.target.closest('.sentence-en');
        if (!enEl) { hideVocabTooltip(); return; }
        const item = enEl.closest('.translation-item');
        if (!item || !item.classList.contains('is-done')) { hideVocabTooltip(); return; }
        const idx = parseInt(item.getAttribute('data-sentence-index'));
        const sent = SENTENCES[idx];
        pendingVocab = { word: text, sentence_en: sent?.en || null, sentence_vi: sent?.vi || null };

        try {
          const range = sel.getRangeAt(0);
          const rect = range.getBoundingClientRect();
          vocabTooltip.style.left = (rect.left + rect.width / 2) + 'px';
          vocabTooltip.style.top = (rect.top - 6) + 'px';
          vocabTooltip.classList.remove('hidden');
        } catch (_) {}
      });

      document.addEventListener('mousedown', function (e) {
        if (!e.target.closest('#vocab-tooltip')) hideVocabTooltip();
      });
    }

    function hideVocabTooltip() {
      vocabTooltip.classList.add('hidden');
      pendingVocab = null;
    }

    function saveVocab() {
      if (!pendingVocab) return;
      vocabSaveBtn.disabled = true;
      vocabSaveBtn.textContent = '⏳ Đang lưu...';

      fetch(VOCAB_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({
          word: pendingVocab.word,
          article_id: ARTICLE_ID,
          sentence_en: pendingVocab.sentence_en,
          sentence_vi: pendingVocab.sentence_vi,
        }),
      })
      .then(r => r.json())
      .then(data => {
        vocabSaveBtn.textContent = data.status === 'created' ? '✅ Đã lưu!' : '✅ Đã có rồi';
        setTimeout(() => {
          hideVocabTooltip();
          vocabSaveBtn.disabled = false;
          vocabSaveBtn.textContent = '📌 Thêm vào sổ từ vựng';
        }, 1400);
      })
      .catch(() => {
        hideVocabTooltip();
        vocabSaveBtn.disabled = false;
        vocabSaveBtn.textContent = '📌 Thêm vào sổ từ vựng';
      });
    }

    // --- Bind events ---
    function bindEvents() {
      const engine = document.getElementById('typing-engine');
      engine.addEventListener('keydown', handleKeydown);
      engine.addEventListener('click', () => engine.focus());
      document.getElementById('typing-wrapper').addEventListener('click', () => engine.focus());
      document.getElementById('completionModal').addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('show');
      });
      engine.addEventListener('paste', e => e.preventDefault());
      engine.addEventListener('keydown', e => { if (e.key === 'Tab') e.preventDefault(); });

      const toggleBtn = document.getElementById('toggleTranslation');
      const panel = document.getElementById('translationPanel');
      if (toggleBtn && panel) {
        toggleBtn.addEventListener('click', () => {
          const willShow = panel.classList.contains('hidden');
          panel.classList.toggle('hidden', !willShow);
          panel.classList.toggle('flex', willShow);
          document.body.classList.toggle('translation-open', willShow);
          toggleBtn.setAttribute('aria-expanded', willShow ? 'true' : 'false');
          toggleBtn.textContent = willShow
            ? '📖 Ẩn bản dịch'
            : `📖 Hiện bản dịch (${TOTAL} câu)`;
          if (willShow) requestAnimationFrame(scrollTranslationToCurrent);
        });
      }
    }

    document.addEventListener('DOMContentLoaded', () => { bindEvents(); init(); });
  </script>
  <x-client.layout.ai-chat-widget />
</body>
</html>
