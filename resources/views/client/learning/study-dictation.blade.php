<!doctype html>
<html lang="vi" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $article->title }} — Dictation Lab</title>
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'canvas': '#F8FAFC',
            'surface': '#FFFFFF',
            'hairline': '#E2E8F0',
            'hairline-subtle': '#F1F5F9',
            'ink': '#0F172A',
            'ink-secondary': '#475569',
            'ink-muted': '#94A3B8',
            'accent': '#0E8A6D',
            'accent-hover': '#0B6E57',
            'accent-tint': '#EBF7F4',
            'error': '#E11D48',
          },
          fontFamily: {
            sans: ['Plus Jakarta Sans', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
            mono: ['JetBrains Mono', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'monospace'],
          },
        },
      },
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.7/dist/purify.min.js"></script>
  <style>
    html, body { height: 100%; overflow: hidden; }
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: #0F172A;
      background: #F8FAFC;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    .ai-explanation-content {
      font-size: 13.5px;
      line-height: 1.65;
      color: #334155;
    }
    .ai-explanation-content p {
      margin-bottom: 0.5rem;
    }
    .ai-explanation-content p:last-child {
      margin-bottom: 0;
    }
    .ai-explanation-content strong {
      color: #0F172A;
      font-weight: 700;
    }
    .ai-explanation-content ul {
      margin: 0.35rem 0;
      padding-left: 1.2rem;
      list-style-type: disc;
    }
    .ai-explanation-content li {
      margin: 0.25rem 0;
    }
    .ai-explanation-content code {
      background: #F1F5F9;
      color: #0E8A6D;
      padding: 0.15rem 0.4rem;
      border-radius: 4px;
      font-family: 'JetBrains Mono', monospace;
      font-size: 12px;
      font-weight: 600;
    }
    :root {
      --text-meaning: 24px;
      --text-typing: 27px;
      --caret-accent: #0E8A6D;
    }
    @media (max-width: 1023px) {
      :root {
        --text-meaning: 18px;
        --text-typing: 21px;
      }
    }
    #typing-engine {
      position: relative;
      font-family: 'JetBrains Mono', ui-monospace, monospace;
      font-size: var(--text-typing);
      line-height: 1.85;
      letter-spacing: 0.02em;
      font-weight: 500;
      cursor: text;
      user-select: none;
      -webkit-user-select: none;
      white-space: pre-wrap;
      font-variant-numeric: tabular-nums;
      border-radius: 12px;
      padding: 10px 14px;
      margin: -10px -14px;
      border: 1.5px solid transparent;
      transition: border-color 0.25s ease, background-color 0.25s ease, box-shadow 0.25s ease;
    }
    #typing-engine:focus { outline: none; }
    #currentMeaning {
      font-size: var(--text-meaning);
      line-height: 1.7;
      letter-spacing: 0.01em;
      text-wrap: balance;
    }

    /* Green completion glow across whole sentence */
    #typing-engine.is-sentence-completed {
      border-color: rgba(16, 185, 129, 0.4);
      background-color: rgba(16, 185, 129, 0.04);
      box-shadow: 0 0 25px rgba(16, 185, 129, 0.18);
      animation: sentence-complete-pulse 0.75s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    #typing-engine.is-sentence-completed .tch {
      color: #047857 !important;
      font-weight: 600;
      text-shadow: 0 0 10px rgba(16, 185, 129, 0.35);
    }
    @keyframes sentence-complete-pulse {
      0% {
        box-shadow: 0 0 0 rgba(16, 185, 129, 0);
        background-color: transparent;
      }
      50% {
        box-shadow: 0 0 32px rgba(16, 185, 129, 0.32);
        background-color: rgba(16, 185, 129, 0.08);
      }
      100% {
        box-shadow: 0 0 22px rgba(16, 185, 129, 0.18);
        background-color: rgba(16, 185, 129, 0.04);
      }
    }
    @keyframes pulse-subtle {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.02); }
    }
    .animate-pulse-subtle {
      animation: pulse-subtle 2s ease-in-out infinite;
    }

    /* Word grouping keeps words unbreakable so per-character transforms never reflow the line. */
    .tw {
      display: inline-block;
      white-space: pre;
    }
    .tch {
      display: inline-block;
      transition: color 120ms linear;
    }
    .char-ghost {
      color: #64748B;
    }
    .char-correct {
      color: #047857;
      font-weight: 600;
    }
    .char-wrong {
      color: #E11D48;
      font-weight: 600;
      background-color: rgba(225, 29, 72, 0.12);
      border-bottom: 2px solid #E11D48;
      border-radius: 2px;
    }
    .char-wrong.is-space {
      min-width: 0.55em;
      background-color: rgba(225, 29, 72, 0.25);
    }

    /* Keystroke micro-animations */
    @keyframes char-strike {
      0%   { transform: translateY(2px) scale(1.32); text-shadow: 0 0 18px rgba(16, 185, 129, 0.6); }
      55%  { transform: translateY(0) scale(0.96); text-shadow: 0 0 8px rgba(16, 185, 129, 0.28); }
      100% { transform: none; text-shadow: none; }
    }
    .char-correct.is-fresh {
      animation: char-strike 240ms cubic-bezier(0.22, 1.35, 0.4, 1) both;
    }
    @keyframes char-miss {
      0%, 100% { transform: translateX(0); }
      20%  { transform: translateX(-2.5px) scale(1.08); }
      45%  { transform: translateX(2.5px) scale(1.08); }
      70%  { transform: translateX(-1.5px); }
    }
    .char-wrong.is-fresh {
      animation: char-miss 220ms cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    }
    @keyframes word-lock {
      0%   { transform: translateY(0); text-shadow: none; }
      40%  { transform: translateY(-3px); text-shadow: 0 3px 14px rgba(16, 185, 129, 0.4); }
      100% { transform: translateY(0); text-shadow: none; }
    }
    .tw.is-locked {
      animation: word-lock 320ms cubic-bezier(0.22, 1.2, 0.36, 1);
    }

    /* Keystroke tactile haptic micro-vibration */
    @keyframes haptic-tap {
      0%   { transform: translateY(0); }
      35%  { transform: translateY(0.85px); }
      100% { transform: translateY(0); }
    }
    .haptic-tap {
      animation: haptic-tap 85ms cubic-bezier(0.2, 0.9, 0.3, 1);
    }

    /* Sliding caret — the primary sense of momentum while typing */
    #typing-caret {
      position: absolute;
      top: 0;
      left: 0;
      width: 2.5px;
      height: 1.2em;
      border-radius: 2px;
      background: linear-gradient(180deg, #10B981, var(--caret-accent));
      box-shadow: 0 0 12px rgba(16, 185, 129, 0.55);
      transform: translate3d(0, 0, 0);
      transition: transform 95ms cubic-bezier(0.2, 0.9, 0.25, 1), opacity 180ms ease;
      will-change: transform;
      pointer-events: none;
    }
    #typing-caret.is-idle {
      animation: caret-blink 1.05s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    #typing-caret.is-blurred {
      opacity: 0.25;
      animation: none;
      box-shadow: none;
    }
    @keyframes caret-blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.15; }
    }

    /* Stage transition between sentences */
    @keyframes stage-in {
      0%   { opacity: 0; transform: translateY(14px); }
      100% { opacity: 1; transform: none; }
    }
    .stage-enter {
      animation: stage-in 320ms cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    /* Floating reward chips */
    #rewardLayer {
      position: absolute;
      inset: 0;
      pointer-events: none;
      z-index: 25;
      overflow: hidden;
    }
    @keyframes reward-rise {
      0%   { opacity: 0; transform: translate(-50%, 14px) scale(0.9); }
      16%  { opacity: 1; transform: translate(-50%, 0) scale(1); }
      70%  { opacity: 1; transform: translate(-50%, -10px) scale(1); }
      100% { opacity: 0; transform: translate(-50%, -34px) scale(0.97); }
    }
    .reward-chip {
      position: absolute;
      left: 50%;
      white-space: nowrap;
      animation: reward-rise 1200ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    /* Combo heat meter */
    #comboFill {
      width: 0%;
      transition: width 220ms cubic-bezier(0.25, 1, 0.5, 1), background-color 300ms ease;
    }
    @keyframes streak-shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-3px); }
      75% { transform: translateX(3px); }
    }
    .streak-reset {
      animation: streak-shake 0.25s ease-in-out;
    }
    #typing-fx-canvas {
      pointer-events: none;
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      z-index: 20;
    }
    .progress-bar-fill {
      transition: width 0.35s cubic-bezier(0.25, 1, 0.5, 1);
    }
    .modal-overlay {
      display: none;
      opacity: 0;
      transition: opacity 0.2s cubic-bezier(0.25, 1, 0.5, 1);
    }
    .modal-overlay.show {
      display: flex;
      opacity: 1;
    }
    .modal-content {
      transform: scale(0.97);
      transition: transform 0.2s cubic-bezier(0.25, 1, 0.5, 1);
    }
    .modal-overlay.show .modal-content {
      transform: scale(1);
    }
    #vocab-tooltip {
      transform: translateX(-50%) translateY(-100%);
    }
    @keyframes vocab-pop {
      0% { opacity: 0; transform: translateX(-50%) translateY(-80%); }
      100% { opacity: 1; transform: translateX(-50%) translateY(-100%); }
    }
    #vocab-tooltip:not(.hidden) {
      animation: vocab-pop 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
    @media (prefers-reduced-motion: reduce) {
      #typing-caret { animation: none !important; transition: none; opacity: 1; }
      .char-correct.is-fresh, .char-wrong.is-fresh, .tw.is-locked, .stage-enter { animation: none; }
      .reward-chip { animation: reward-rise 1200ms linear forwards; }
      .progress-bar-fill, #comboFill { transition: none; }
      .modal-overlay, .modal-content { transition: none; }
    }
    .hud-num {
      font-variant-numeric: tabular-nums;
    }
  </style>
</head>
<body class="bg-canvas h-dvh max-h-dvh overflow-hidden flex flex-col select-none">
  @php
    $blocks = $article->contentBlocks();
    $sentences = $article->sentences();
    $sentenceTotal = count($sentences);
    $isPro = auth()->user()->isPro();
  @endphp

  {{-- TOP NAVIGATION & INSTRUMENT HUD BAR --}}
  <header class="bg-white border-b border-hairline flex-shrink-0 z-40 relative">
    <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16 gap-4">

      {{-- Left: Back to Library & Article Identifier --}}
      <div class="flex items-center gap-3.5 min-w-0">
        <a href="{{ route('client.articles.library') }}"
           class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-ink-secondary hover:text-ink hover:bg-slate-100 transition-colors flex-shrink-0 border border-hairline cursor-pointer"
           title="Quay lại thư viện bài viết">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
          </svg>
        </a>

        <div class="min-w-0 flex items-center gap-2.5">
          @if($article->categories->isNotEmpty())
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-accent-tint text-accent border border-emerald-200/70 flex-shrink-0">
              {{ $article->categories->first()->name }}
            </span>
          @endif
          <h1 class="text-base sm:text-lg font-bold text-ink truncate max-w-xs sm:max-w-md lg:max-w-xl" title="{{ $article->title }}">
            {{ $article->title }}
          </h1>
        </div>
      </div>

      {{-- Center: Restrained Tabular Metrics HUD (Desktop) --}}
      <div class="hidden md:flex items-center gap-6 px-5 py-2 rounded-full bg-slate-50 border border-hairline text-sm">
        <div class="flex items-center gap-2">
          <span class="text-ink-muted text-xs font-bold uppercase tracking-wider">WPM</span>
          <span class="font-extrabold text-ink hud-num text-base" id="wpmDisplay">0</span>
        </div>

        <div class="w-px h-3.5 bg-hairline"></div>

        <div class="flex items-center gap-2">
          <span class="text-ink-muted text-xs font-bold uppercase tracking-wider">Chính xác</span>
          <span class="font-extrabold text-accent hud-num text-base" id="accDisplay">100%</span>
        </div>

        <div class="w-px h-3.5 bg-hairline"></div>

        <div class="flex items-center gap-2">
          <span class="text-ink-muted text-xs font-bold uppercase tracking-wider">Tiến độ</span>
          <span class="font-extrabold text-ink hud-num text-base" id="progressDisplay">0 / {{ $sentenceTotal }}</span>
        </div>
      </div>

      {{-- Right: Audio & Controls --}}
      <div class="flex items-center gap-2.5 flex-shrink-0">
        {{-- Speech Rate Button --}}
        <button id="speechRateBtn" onclick="cycleSpeechRate()"
                title="Tốc độ giọng đọc"
                class="hidden sm:inline-flex items-center px-3 py-1.5 rounded-lg border border-hairline text-xs font-mono font-bold text-ink-secondary hover:text-ink hover:bg-slate-50 transition-colors cursor-pointer shadow-2xs">
          <span id="speechRateText">1.0x</span>
        </button>

        {{-- Sound FX Toggle --}}
        <button id="soundToggle" onclick="toggleSound()"
                title="Bật/tắt âm thanh gõ phím"
                class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-hairline text-ink-secondary hover:text-ink hover:bg-slate-50 transition-colors cursor-pointer shadow-2xs">
          <svg id="soundIconOn" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.216-1.614.322-2.396.234-.847 1.058-1.354 1.938-1.354h2.24z"/>
          </svg>
          <svg id="soundIconOff" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-4.5l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.216-1.614.322-2.396.234-.847 1.058-1.354 1.938-1.354h2.24z"/>
          </svg>
        </button>

        {{-- Reset Session Button --}}
        <button onclick="confirmResetSession()"
                title="Bắt đầu lại bài học"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-hairline text-xs sm:text-sm font-semibold text-ink-secondary hover:text-error hover:border-error/40 hover:bg-rose-50/40 transition-colors cursor-pointer shadow-2xs">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/>
          </svg>
          <span class="hidden sm:inline">Làm lại</span>
        </button>
      </div>
    </div>

    {{-- Restrained 2px Progress Indicator --}}
    <div class="h-1 bg-hairline-subtle w-full overflow-hidden">
      <div class="progress-bar-fill h-full bg-accent" id="progressBar" style="width: 0%"></div>
    </div>
  </header>

  {{-- RESTORE TOAST --}}
  <div id="restoreBanner" class="hidden fixed bottom-6 left-6 z-50 bg-slate-900 text-white px-4 py-3 rounded-xl shadow-float flex items-center gap-3 text-sm border border-slate-800">
    <div class="w-2.5 h-2.5 rounded-full bg-accent animate-pulse"></div>
    <span id="restoreBannerText" class="text-slate-100 font-medium">Đã khôi phục tiến độ bài học.</span>
    <button onclick="dismissRestoreBanner()" class="text-slate-400 hover:text-white ml-2 cursor-pointer">✕</button>
  </div>

  {{-- MOBILE SEGMENTED VIEW SWITCHER --}}
  <div class="lg:hidden flex items-center border-b border-hairline bg-white px-3 py-2 flex-shrink-0 gap-2">
    <button id="mobileTabDictation" onclick="switchMobileTab('dictation')"
            class="flex-1 py-2 px-3 text-xs sm:text-sm font-bold rounded-lg bg-ink text-white transition-all flex items-center justify-center gap-1.5 cursor-pointer">
      Chép chính tả
    </button>
    <button id="mobileTabTranslation" onclick="switchMobileTab('translation')"
            class="flex-1 py-2 px-3 text-xs sm:text-sm font-bold rounded-lg bg-slate-100 text-ink-secondary hover:bg-slate-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
      Bản dịch (<span id="mobileDoneCount">0</span> / {{ $sentenceTotal }})
    </button>
  </div>

  {{-- MAIN WORKSPACE: DICTATION STAGE + COMPANION PANEL --}}
  <main class="flex-1 min-h-0 w-full flex flex-col lg:flex-row overflow-hidden">

    {{-- LEFT: DICTATION STAGE (FOCUSED INSTRUMENT) --}}
    <section id="dictationPanel" class="relative flex-1 lg:w-[64%] min-h-0 bg-white flex flex-col overflow-hidden border-b lg:border-b-0 lg:border-r border-hairline">
      {{-- Particle FX Canvas --}}
      <canvas id="typing-fx-canvas"></canvas>

      {{-- Stage Sub-Header --}}
      <div class="px-4 sm:px-10 lg:px-12 py-2.5 flex items-center justify-between gap-2 flex-shrink-0 text-sm">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
          <span class="font-extrabold text-ink text-sm sm:text-base whitespace-nowrap">
            Câu <span id="sentenceLabel" class="text-accent hud-num">1</span> trên {{ $sentenceTotal }}
          </span>
          <span class="text-hairline hidden sm:inline">•</span>
          <span class="text-ink-secondary text-xs sm:text-sm whitespace-nowrap">
            <span id="charCount" class="hud-num text-ink font-semibold">0</span> / <span id="totalChars" class="hud-num font-medium">0</span><span class="hidden sm:inline"> ký tự</span>
          </span>
          {{-- Dynamic Streak Badge --}}
          <div id="streakBadge" class="hidden items-center gap-1.5 px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-bold whitespace-nowrap transition-all duration-200">
            <span id="streakIcon">⚡</span>
            <span id="streakCount">0</span>
            <span class="hidden sm:inline text-[10px] uppercase tracking-wider font-semibold opacity-80">streak</span>
          </div>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
          {{-- Audio Play Button --}}
          <button type="button" onclick="speakCurrentSentence()" id="listenBtn"
                  aria-label="Nghe câu tiếng Anh"
                  class="inline-flex items-center justify-center gap-2 min-w-[44px] min-h-[44px] sm:min-h-0 px-3 sm:px-3.5 sm:py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-accent text-xs sm:text-sm font-bold transition-all cursor-pointer shadow-2xs hover:bg-emerald-100"
                  title="Nghe câu tiếng Anh (Ctrl + Space)">
            <svg class="w-4 h-4 text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.216-1.614.322-2.396.234-.847 1.058-1.354 1.938-1.354h2.24z"/>
            </svg>
            <span class="hidden sm:inline">Nghe câu</span>
            <kbd class="hidden lg:inline px-1.5 py-0.5 rounded bg-white text-[11px] text-ink-secondary font-mono border border-emerald-200">Ctrl + Space</kbd>
          </button>

          {{-- Hint Button --}}
          <button type="button" onclick="hintNextChar()"
                  title="Gợi ý ký tự tiếp theo (Tab)"
                  class="inline-flex items-center justify-center gap-1.5 min-h-[44px] sm:min-h-0 px-3 sm:py-2 rounded-xl border border-hairline bg-white hover:bg-slate-50 text-ink-secondary hover:text-ink text-xs sm:text-sm font-semibold transition-colors cursor-pointer shadow-2xs whitespace-nowrap">
            <span>Gợi ý</span>
            <kbd class="hidden lg:inline px-1.5 py-0.5 rounded bg-slate-100 text-[11px] text-ink-muted font-mono border border-slate-200">Tab</kbd>
          </button>
        </div>
      </div>

      {{-- Combo Heat Meter: fills toward the next streak milestone, empties on a miss --}}
      <div class="h-[3px] w-full bg-hairline-subtle flex-shrink-0 overflow-hidden">
        <div id="comboFill" class="h-full rounded-r-full bg-accent"></div>
      </div>

      {{-- Stage: prompt and typing surface travel together as one vertically centred group,
           so the reading eye never jumps across dead space between them. --}}
      <div id="typing-wrapper" class="flex-1 min-h-0 overflow-y-auto cursor-text flex flex-col pb-[7vh]">
        <div class="my-auto w-full py-6">

          {{-- Meaning Prompt --}}
          <div class="px-4 sm:px-10 lg:px-12">
            <div class="flex items-center justify-between mb-1.5">
              <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                <span class="text-[11px] font-bold text-accent uppercase tracking-[0.14em]">Nghĩa tiếng Việt</span>
              </div>
              <div id="section-heading" class="hidden">
                <span id="section-heading-text" class="text-[11px] font-semibold tracking-wide text-ink-muted bg-white border border-hairline px-2 py-0.5 rounded-md"></span>
              </div>
            </div>

            <p id="currentMeaning" class="font-bold text-ink tracking-normal select-text leading-relaxed">
              Đang tải dữ liệu bài học...
            </p>
          </div>

          {{-- Hairline seam between prompt and typing surface --}}
          <div class="mx-4 sm:mx-10 lg:mx-12 mt-5 h-px bg-gradient-to-r from-transparent via-hairline to-transparent"></div>

          {{-- Typing Arena --}}
          <div class="px-4 sm:px-10 lg:px-12 pt-6">
            <div id="typing-engine" tabindex="0" autofocus></div>

            {{-- Next Sentence Prompt Banner (shown when current sentence finishes, waiting for Enter) --}}
            <div id="nextSentencePrompt" class="hidden mt-6 flex-wrap items-center gap-3 animate-pulse-subtle">
              <button type="button" onclick="advanceToNextSentence()"
                      id="nextSentenceBtn"
                      class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-accent hover:bg-accent-hover text-white text-xs sm:text-sm font-bold shadow-md shadow-accent/20 cursor-pointer transition-all hover:scale-[1.02] active:scale-[0.98]">
                <span id="nextSentenceBtnText">Sang câu tiếp</span>
                <kbd class="px-1.5 py-0.5 rounded bg-white/20 text-[11px] font-mono border border-white/30 text-white">Enter ↵</kbd>
              </button>
              <span class="text-xs text-ink-muted flex items-center gap-1.5">
                <span>Nhấn</span>
                <kbd class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-200 text-ink font-mono text-[11px]">Enter</kbd>
                <span id="nextSentenceHintText">để sang câu tiếp theo</span>
              </span>
            </div>
          </div>
        </div>
      </div>

      {{-- Floating reward chips (streak milestones, sentence completions) --}}
      <div id="rewardLayer"></div>

      {{-- Footer Bar --}}
      <div class="px-4 sm:px-10 lg:px-12 py-2.5 border-t border-hairline flex items-center justify-between flex-shrink-0 text-[11px] sm:text-xs text-ink-secondary">
        <div class="flex items-center gap-3.5">
          <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-accent"></span>
            <span class="font-semibold text-ink-secondary">Đúng</span>
          </span>
          <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-error"></span>
            <span class="font-semibold text-ink-secondary">Sai</span>
          </span>
          <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
            <span class="font-semibold text-ink-secondary">Chưa gõ</span>
          </span>
        </div>

        <div class="hidden sm:flex items-center gap-2.5 text-ink-muted">
          <span><kbd class="px-1.5 py-0.5 rounded bg-slate-100 font-mono text-ink text-[11px] border border-slate-200">Backspace</kbd> sửa lỗi</span>
          <span>•</span>
          <span>Click để gõ</span>
        </div>
      </div>
    </section>

    {{-- RIGHT: COMPANION TRANSLATION PANEL (36% Desktop) --}}
    <aside id="translationPanel" class="hidden lg:flex flex-col lg:w-[36%] min-h-0 bg-white overflow-hidden">
      <div class="px-6 py-4 border-b border-hairline flex items-center justify-between flex-shrink-0 text-sm">
        <span class="font-bold text-ink text-sm sm:text-base">Bản dịch toàn bài</span>
        <span class="text-ink-secondary hud-num text-xs sm:text-sm">
          <span id="asideDoneCount" class="font-bold text-accent">0</span> / {{ $sentenceTotal }} đã xong
        </span>
      </div>

      {{-- Sentence List --}}
      <div id="translationList" class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-5 space-y-2"></div>
    </aside>
  </main>

  {{-- FLOATING VOCABULARY TOOLTIP --}}
  <div id="vocab-tooltip" class="hidden fixed z-50 pointer-events-auto">
    <div class="bg-slate-900 text-white rounded-lg shadow-float px-3 py-1.5 flex items-center gap-2 text-xs border border-slate-800">
      <button id="vocab-save-btn" onclick="saveVocab()" class="font-medium text-emerald-400 hover:text-emerald-300 transition-colors cursor-pointer flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
        </svg>
        <span>Lưu từ vựng</span>
      </button>
    </div>
  </div>

  {{-- CONFIRM RESET MODAL --}}
  <div id="resetConfirmModal" class="modal-overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs items-center justify-center p-4">
    <div class="modal-content bg-white rounded-2xl w-full max-w-sm shadow-float p-6 text-center border border-hairline">
      <h3 class="text-base font-bold text-ink">Bắt đầu lại bài học?</h3>
      <p class="text-xs text-ink-secondary mt-1.5 leading-relaxed">
        Tiến độ hiện tại của bạn sẽ bị xóa và bài học sẽ quay về câu số 1.
      </p>
      <div class="flex items-center gap-2 mt-6">
        <button onclick="dismissResetModal()" class="flex-1 py-2 rounded-lg border border-hairline text-xs font-semibold text-ink-secondary hover:bg-slate-50 transition-colors cursor-pointer">
          Hủy
        </button>
        <button onclick="executeResetSession()" class="flex-1 py-2 rounded-lg bg-error hover:bg-rose-700 text-white text-xs font-semibold transition-colors cursor-pointer">
          Xác nhận làm lại
        </button>
      </div>
    </div>
  </div>

  {{-- COMPLETION SCORECARD MODAL --}}
  <div id="completionModal" class="modal-overlay fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs items-center justify-center p-4">
    <div class="modal-content bg-white rounded-2xl w-full max-w-md shadow-float overflow-hidden border border-hairline">
      <div class="p-8 text-center border-b border-hairline">
        <div class="w-12 h-12 rounded-full bg-accent-tint text-accent flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
          </svg>
        </div>
        <h2 class="text-xl font-bold text-ink">Hoàn thành bài chép!</h2>
        <p class="text-xs text-ink-secondary mt-1">Bạn đã hoàn thành tất cả {{ $sentenceTotal }} câu trong bài học.</p>
      </div>

      {{-- Metrics --}}
      <div class="p-6 grid grid-cols-4 gap-2 text-center bg-slate-50/50 border-b border-hairline">
        <div>
          <p class="text-lg font-bold text-ink hud-num" id="modalWpm">0</p>
          <p class="text-[10px] text-ink-muted uppercase mt-0.5">WPM</p>
        </div>
        <div>
          <p class="text-lg font-bold text-accent hud-num" id="modalAcc">100%</p>
          <p class="text-[10px] text-ink-muted uppercase mt-0.5">Chính xác</p>
        </div>
        <div>
          <p class="text-lg font-bold text-ink hud-num" id="modalTime">0:00</p>
          <p class="text-[10px] text-ink-muted uppercase mt-0.5">Thời gian</p>
        </div>
        <div>
          <p class="text-lg font-bold text-ink hud-num" id="modalSentences">0</p>
          <p class="text-[10px] text-ink-muted uppercase mt-0.5">Số câu</p>
        </div>
      </div>

      {{-- Navigation --}}
      <div class="p-6 space-y-2">
        <a href="{{ route('client.articles.library') }}"
           class="flex items-center justify-center gap-2 w-full py-2.5 bg-ink text-white text-xs font-semibold rounded-lg hover:bg-slate-800 transition-colors cursor-pointer">
          Về thư viện bài viết
        </a>
        <button onclick="executeResetSession()"
                class="w-full py-2.5 border border-hairline text-xs font-medium text-ink-secondary rounded-lg hover:bg-slate-50 transition-colors cursor-pointer">
          Chép lại bài này
        </button>
      </div>
    </div>
  </div>

  {{-- ENGINE SCRIPT --}}
  <script>
    const BLOCKS = @json($blocks, JSON_UNESCAPED_UNICODE);
    const SENTENCES = @json($sentences, JSON_UNESCAPED_UNICODE);
    const ARTICLE_ID = {{ $article->id }};
    const USER_ID = {{ auth()->id() }};
    const SAVE_URL = @json(route('client.learning.dictation.save'));
    const EXPLAIN_URL = @json(route('client.learning.dictation.explain'));
    const VOCAB_URL = @json(route('client.vocabulary.store'));
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const IS_PRO = {{ $isPro ? 'true' : 'false' }};
    const TOTAL = SENTENCES.length;
    const STORAGE_KEY = `dictation_session_${ARTICLE_ID}_${USER_ID}`;

    const CHAR_EQUIVALENCE = {
      '\u2014': '-', '\u2013': '-', '\u2018': "'", '\u2019': "'", '\u201C': '"', '\u201D': '"', '\u2026': '.',
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
    let currentStreak = 0;
    let maxStreak = 0;
    let sentenceMistakes = 0;
    let startTime = null;
    let accumulatedTimeMs = 0;
    let isCompleted = false;
    let isWaitingForNext = false;
    let soundEnabled = true;
    let speechRate = 1.0;
    let audioCtx = null;
    let speechSynth = window.speechSynthesis || null;
    let saveTimeout = null;
    let caretEl = null;
    let caretIdleTimer = null;

    const REDUCED_MOTION = window.matchMedia
      ? window.matchMedia('(prefers-reduced-motion: reduce)').matches
      : false;

    // --- High-Performance Typing Particle Canvas FX ---
    let fxCanvas = null;
    let fxCtx = null;
    let fxParticles = [];
    let fxRings = [];
    let isFxLoopRunning = false;
    let canvasW = 0;
    let canvasH = 0;

    const SPARK_COLORS_NORMAL = ['#10B981', '#34D399', '#06B6D4', '#6EE7B7', '#FBBF24'];
    const SPARK_COLORS_COMBO = ['#F59E0B', '#FBBF24', '#8B5CF6', '#EC4899', '#10B981', '#38BDF8'];

    function initFxCanvas() {
      fxCanvas = document.getElementById('typing-fx-canvas');
      if (!fxCanvas) return;
      fxCtx = fxCanvas.getContext('2d');
      resizeFxCanvas();
      window.addEventListener('resize', resizeFxCanvas);
    }

    function resizeFxCanvas() {
      const panel = document.getElementById('dictationPanel');
      if (!panel || !fxCanvas || !fxCtx) return;
      const rect = panel.getBoundingClientRect();
      const dpr = Math.min(window.devicePixelRatio || 1, 2);
      canvasW = rect.width;
      canvasH = rect.height;
      fxCanvas.width = canvasW * dpr;
      fxCanvas.height = canvasH * dpr;
      fxCtx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    function triggerTypingEffect(charEl, isCorrect, streak) {
      if (!fxCanvas || !fxCtx || !charEl) return;
      const panel = document.getElementById('dictationPanel');
      if (!panel) return;

      const pRect = panel.getBoundingClientRect();
      const cRect = charEl.getBoundingClientRect();
      const x = cRect.left - pRect.left + cRect.width / 2;
      const y = cRect.top - pRect.top + cRect.height / 2;

      if (y < -20 || y > pRect.height + 20) return;

      if (isCorrect) {
        // Shockwave ripple ring
        const ringColor = streak >= 50 ? '#F43F5E' : (streak >= 30 ? '#8B5CF6' : (streak >= 15 ? '#F59E0B' : '#10B981'));
        fxRings.push({
          x,
          y,
          r: 4,
          maxR: 16 + Math.min(streak * 0.2, 12),
          alpha: 0.5,
          color: ringColor,
          grow: 1.6,
        });

        // Spark lift: a narrow upward cone reads cleaner than an omnidirectional scatter.
        const palette = streak >= 15 ? SPARK_COLORS_COMBO : SPARK_COLORS_NORMAL;
        const count = streak >= 25 ? 5 : 3;

        for (let i = 0; i < count; i++) {
          const angle = -Math.PI / 2 + (Math.random() - 0.5) * 1.5;
          const speed = 1.1 + Math.random() * 1.9;
          fxParticles.push({
            x,
            y,
            vx: Math.cos(angle) * speed,
            vy: Math.sin(angle) * speed,
            size: 1.6 + Math.random() * 1.6,
            color: palette[Math.floor(Math.random() * palette.length)],
            alpha: 0.95,
            decay: 0.035 + Math.random() * 0.025,
            drag: 0.93,
            gravity: 0.05,
            glow: true,
          });
        }

        // Streak Milestone Celebration
        if (streak > 0 && (streak === 10 || streak === 25 || streak === 50 || streak === 100 || (streak > 100 && streak % 50 === 0))) {
          for (let i = 0; i < 16; i++) {
            const angle = Math.random() * Math.PI * 2;
            const speed = 2.0 + Math.random() * 3.5;
            fxParticles.push({
              x,
              y,
              vx: Math.cos(angle) * speed,
              vy: Math.sin(angle) * speed - 1.2,
              size: 2.5 + Math.random() * 2.5,
              color: SPARK_COLORS_COMBO[Math.floor(Math.random() * SPARK_COLORS_COMBO.length)],
              alpha: 1.0,
              decay: 0.02 + Math.random() * 0.015,
              drag: 0.95,
              gravity: 0.08,
              glow: true,
            });
          }

          announceMilestone(streak);
        }
      } else {
        // Red drift embers on mistake
        for (let i = 0; i < 3; i++) {
          fxParticles.push({
            x,
            y,
            vx: (Math.random() - 0.5) * 1.6,
            vy: 0.4 + Math.random() * 1.0,
            size: 2.0,
            color: '#F43F5E',
            alpha: 0.85,
            decay: 0.035,
            drag: 0.92,
            gravity: 0.05,
          });
        }
      }

      if (!isFxLoopRunning) {
        isFxLoopRunning = true;
        requestAnimationFrame(runFxLoop);
      }
    }

    function runFxLoop() {
      if (!fxCtx || !fxCanvas) {
        isFxLoopRunning = false;
        return;
      }

      fxCtx.clearRect(0, 0, canvasW, canvasH);

      // 1. Draw and update rings
      for (let i = fxRings.length - 1; i >= 0; i--) {
        const ring = fxRings[i];
        ring.r += ring.grow;
        ring.alpha -= 0.04;
        if (ring.alpha <= 0 || ring.r >= ring.maxR) {
          fxRings.splice(i, 1);
          continue;
        }
        fxCtx.save();
        fxCtx.beginPath();
        fxCtx.arc(ring.x, ring.y, ring.r, 0, Math.PI * 2);
        fxCtx.strokeStyle = ring.color;
        fxCtx.globalAlpha = Math.max(0, ring.alpha);
        fxCtx.lineWidth = 1.6;
        fxCtx.stroke();
        fxCtx.restore();
      }

      // 2. Draw and update particles
      for (let i = fxParticles.length - 1; i >= 0; i--) {
        const p = fxParticles[i];
        p.x += p.vx;
        p.y += p.vy;
        p.vx *= p.drag;
        p.vy = (p.vy * p.drag) + p.gravity;
        p.alpha -= p.decay;
        p.size = Math.max(0.2, p.size - 0.03);

        if (p.alpha <= 0 || p.size <= 0.3) {
          fxParticles.splice(i, 1);
          continue;
        }

        fxCtx.save();
        fxCtx.globalAlpha = Math.max(0, p.alpha);
        fxCtx.fillStyle = p.color;
        if (p.glow) {
          fxCtx.shadowBlur = 8;
          fxCtx.shadowColor = p.color;
        }
        fxCtx.beginPath();
        fxCtx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        fxCtx.fill();
        fxCtx.restore();
      }

      if (fxParticles.length > 0 || fxRings.length > 0) {
        requestAnimationFrame(runFxLoop);
      } else {
        isFxLoopRunning = false;
        fxCtx.clearRect(0, 0, canvasW, canvasH);
      }
    }

    function updateStreakUI(streak) {
      const badge = document.getElementById('streakBadge');
      const countEl = document.getElementById('streakCount');
      const iconEl = document.getElementById('streakIcon');
      updateComboBar(streak);
      if (!badge || !countEl || !iconEl) return;

      if (streak < 5) {
        if (!badge.classList.contains('hidden') && streak === 0) {
          badge.classList.add('streak-reset');
          setTimeout(() => {
            badge.classList.add('hidden');
            badge.classList.remove('flex', 'streak-reset');
          }, 220);
        } else {
          badge.classList.add('hidden');
          badge.classList.remove('flex');
        }
        return;
      }

      badge.classList.remove('hidden', 'streak-reset');
      badge.classList.add('flex');
      countEl.textContent = streak;

      if (streak >= 50) {
        badge.className = 'flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200/80 shadow-xs ring-2 ring-rose-200/50 transition-all duration-200';
        iconEl.textContent = '👑';
      } else if (streak >= 30) {
        badge.className = 'flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-violet-50 text-violet-600 border border-violet-200/80 shadow-2xs transition-all duration-200';
        iconEl.textContent = '🚀';
      } else if (streak >= 15) {
        badge.className = 'flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200/80 shadow-2xs transition-all duration-200';
        iconEl.textContent = '🔥';
      } else {
        badge.className = 'flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 transition-all duration-200';
        iconEl.textContent = '⚡';
      }
    }

    // --- ASMR Mechanical Keyboard Synthesizer (Web Audio API) ---
    let noiseBuffer = null;
    function getNoiseBuffer() {
      if (!noiseBuffer && audioCtx) {
        const bufferSize = Math.floor(audioCtx.sampleRate * 0.06);
        noiseBuffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
        const data = noiseBuffer.getChannelData(0);
        for (let i = 0; i < bufferSize; i++) {
          data[i] = Math.random() * 2 - 1;
        }
      }
      return noiseBuffer;
    }

    function triggerHaptic(isCorrect = true) {
      if (typeof navigator !== 'undefined' && navigator.vibrate) {
        try {
          navigator.vibrate(isCorrect ? 12 : [18, 30, 18]);
        } catch (_) {}
      }
      const engine = document.getElementById('typing-engine');
      if (engine && !REDUCED_MOTION) {
        engine.classList.remove('haptic-tap');
        void engine.offsetWidth;
        engine.classList.add('haptic-tap');
      }
    }

    function playKeySound(isCorrect, isSpace = false) {
      if (!soundEnabled) return;
      try {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') audioCtx.resume();
        const now = audioCtx.currentTime;

        if (isCorrect) {
          // 1. Crisp tactile stem snap (high bandpass noise burst)
          const buffer = getNoiseBuffer();
          if (buffer) {
            const noise = audioCtx.createBufferSource();
            noise.buffer = buffer;

            const noiseFilter = audioCtx.createBiquadFilter();
            noiseFilter.type = 'bandpass';
            const clickFreq = isSpace ? 1800 : 2800 + (Math.random() - 0.5) * 500;
            noiseFilter.frequency.setValueAtTime(clickFreq, now);
            noiseFilter.Q.setValueAtTime(2.4, now);

            const noiseGain = audioCtx.createGain();
            const clickVol = isSpace ? 0.22 : 0.28;
            noiseGain.gain.setValueAtTime(clickVol, now);
            noiseGain.gain.exponentialRampToValueAtTime(0.001, now + (isSpace ? 0.024 : 0.016));

            noise.connect(noiseFilter);
            noiseFilter.connect(noiseGain);
            noiseGain.connect(audioCtx.destination);
            noise.start(now);
            noise.stop(now + 0.03);
          }

          // 2. Creamy bottom-out "thock" (lowpass filtered resonant body)
          const bodyOsc = audioCtx.createOscillator();
          const bodyGain = audioCtx.createGain();
          const bodyFilter = audioCtx.createBiquadFilter();

          bodyOsc.type = 'triangle';
          const baseFreq = isSpace ? 145 : 225 + (Math.random() - 0.5) * 40;
          const endFreq = isSpace ? 60 : 90;
          bodyOsc.frequency.setValueAtTime(baseFreq, now);
          bodyOsc.frequency.exponentialRampToValueAtTime(endFreq, now + (isSpace ? 0.065 : 0.048));

          bodyFilter.type = 'lowpass';
          bodyFilter.frequency.setValueAtTime(isSpace ? 380 : 540, now);
          bodyFilter.Q.setValueAtTime(1.8, now);

          const bodyVol = isSpace ? 0.38 : 0.34;
          bodyGain.gain.setValueAtTime(bodyVol, now);
          bodyGain.gain.exponentialRampToValueAtTime(0.001, now + (isSpace ? 0.075 : 0.055));

          bodyOsc.connect(bodyFilter);
          bodyFilter.connect(bodyGain);
          bodyGain.connect(audioCtx.destination);
          bodyOsc.start(now);
          bodyOsc.stop(now + (isSpace ? 0.08 : 0.06));

          // 3. Sub-bass acoustic deskmat weight (deep tactile thump)
          const subOsc = audioCtx.createOscillator();
          const subGain = audioCtx.createGain();
          subOsc.type = 'sine';
          const subFreq = isSpace ? 80 : 110;
          subOsc.frequency.setValueAtTime(subFreq, now);
          subOsc.frequency.exponentialRampToValueAtTime(40, now + 0.04);

          subGain.gain.setValueAtTime(isSpace ? 0.22 : 0.18, now);
          subGain.gain.exponentialRampToValueAtTime(0.001, now + 0.045);

          subOsc.connect(subGain);
          subGain.connect(audioCtx.destination);
          subOsc.start(now);
          subOsc.stop(now + 0.05);

        } else {
          // Soft wooden tock on mistake (distinguishable feedback without being jarring)
          const osc = audioCtx.createOscillator();
          const gain = audioCtx.createGain();
          const filter = audioCtx.createBiquadFilter();

          osc.type = 'triangle';
          osc.frequency.setValueAtTime(160, now);
          osc.frequency.exponentialRampToValueAtTime(65, now + 0.06);

          filter.type = 'lowpass';
          filter.frequency.setValueAtTime(320, now);

          gain.gain.setValueAtTime(0.28, now);
          gain.gain.exponentialRampToValueAtTime(0.001, now + 0.065);

          osc.connect(filter);
          filter.connect(gain);
          gain.connect(audioCtx.destination);
          osc.start(now);
          osc.stop(now + 0.07);
        }
      } catch (_) {}
    }

    // --- Audio Feedback: Melodic Completion Chime ---
    function playTing() {
      if (!soundEnabled) return;
      try {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') audioCtx.resume();
        const now = audioCtx.currentTime;

        const notes = [1046.5, 1318.5, 1568.0];
        notes.forEach((freq, idx) => {
          const osc = audioCtx.createOscillator();
          const gain = audioCtx.createGain();
          osc.type = 'sine';
          osc.frequency.setValueAtTime(freq, now + idx * 0.04);

          gain.gain.setValueAtTime(0.14, now + idx * 0.04);
          gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.04 + 0.35);

          osc.connect(gain);
          gain.connect(audioCtx.destination);
          osc.start(now + idx * 0.04);
          osc.stop(now + idx * 0.04 + 0.38);
        });
      } catch (_) {}
    }

    function toggleSound() {
      soundEnabled = !soundEnabled;
      updateSoundUI();
      saveProgressToStorage();
    }

    function updateSoundUI() {
      const onIcon = document.getElementById('soundIconOn');
      const offIcon = document.getElementById('soundIconOff');
      if (onIcon && offIcon) {
        onIcon.classList.toggle('hidden', !soundEnabled);
        offIcon.classList.toggle('hidden', soundEnabled);
      }
    }

    // --- Text-to-Speech (Pronunciation) ---
    function cycleSpeechRate() {
      const rates = [0.8, 1.0, 1.2];
      const curIdx = rates.indexOf(speechRate);
      speechRate = rates[(curIdx + 1) % rates.length];
      document.getElementById('speechRateText').textContent = speechRate + 'x';
      saveProgressToStorage();
      speakCurrentSentence();
    }

    function speakCurrentSentence() {
      if (!speechSynth) return;
      const sent = currentSentence();
      if (!sent || !sent.en) return;

      speechSynth.cancel();
      const utter = new SpeechSynthesisUtterance(sent.en);
      utter.lang = 'en-US';
      utter.rate = speechRate;

      const btn = document.getElementById('listenBtn');
      if (btn) btn.classList.add('bg-emerald-50', 'border-accent');

      utter.onend = () => {
        if (btn) btn.classList.remove('bg-emerald-50', 'border-accent');
      };
      utter.onerror = () => {
        if (btn) btn.classList.remove('bg-emerald-50', 'border-accent');
      };

      speechSynth.speak(utter);
    }

    // --- Persistence (Local Storage) ---
    function saveProgressToStorage() {
      if (isCompleted || !TOTAL) return;
      const elapsed = startTime ? (Date.now() - startTime) + accumulatedTimeMs : accumulatedTimeMs;
      const payload = {
        articleId: ARTICLE_ID,
        userId: USER_ID,
        currentIdx,
        cursorPos,
        charStates,
        totalCorrect,
        totalKeystrokes,
        currentStreak,
        elapsedMs: elapsed,
        soundEnabled,
        speechRate,
        timestamp: Date.now(),
      };
      try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
      } catch (_) {}
    }

    function debouncedSave() {
      if (saveTimeout) clearTimeout(saveTimeout);
      saveTimeout = setTimeout(saveProgressToStorage, 300);
    }

    function loadProgressFromStorage() {
      try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return false;
        const data = JSON.parse(raw);
        if (!data || data.articleId !== ARTICLE_ID || data.userId !== USER_ID) return false;

        if (typeof data.soundEnabled === 'boolean') {
          soundEnabled = data.soundEnabled;
          updateSoundUI();
        }
        if (typeof data.speechRate === 'number') {
          speechRate = data.speechRate;
          document.getElementById('speechRateText').textContent = speechRate + 'x';
        }

        if (typeof data.currentIdx === 'number' && data.currentIdx > 0 && data.currentIdx < TOTAL) {
          currentIdx = data.currentIdx;
          cursorPos = data.cursorPos || 0;
          charStates = Array.isArray(data.charStates) ? data.charStates : [];
          totalCorrect = data.totalCorrect || 0;
          totalKeystrokes = data.totalKeystrokes || 0;
          accumulatedTimeMs = data.elapsedMs || 0;
          if (typeof data.currentStreak === 'number') {
            currentStreak = data.currentStreak;
            updateStreakUI(currentStreak);
          }

          showRestoreBanner(currentIdx + 1, TOTAL);
          return true;
        }
      } catch (_) {}
      return false;
    }

    function clearProgressFromStorage() {
      try {
        localStorage.removeItem(STORAGE_KEY);
      } catch (_) {}
    }

    function showRestoreBanner(cur, total) {
      const banner = document.getElementById('restoreBanner');
      const text = document.getElementById('restoreBannerText');
      if (!banner || !text) return;
      text.textContent = `Đã khôi phục câu ${cur} / ${total}.`;
      banner.classList.remove('hidden');
      setTimeout(dismissRestoreBanner, 4000);
    }

    function dismissRestoreBanner() {
      const banner = document.getElementById('restoreBanner');
      if (banner) banner.classList.add('hidden');
    }

    // --- Core Helpers ---
    function currentSentence() { return SENTENCES[currentIdx] ?? null; }

    function escapeHtml(str) {
      const map = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};
      return str.replace(/[&<>"']/g, c => map[c]);
    }

    function scrollIntoContainer(element, container, { block = 'nearest', margin = 16 } = {}) {
      if (!element || !container) return;
      const elRect = element.getBoundingClientRect();
      const containerRect = container.getBoundingClientRect();
      let delta = 0;

      if (block === 'center') {
        delta = elRect.top - containerRect.top - (containerRect.height / 2) + (elRect.height / 2);
      } else {
        if (elRect.top < containerRect.top + margin) {
          delta = elRect.top - containerRect.top - margin;
        } else if (elRect.bottom > containerRect.bottom - margin) {
          delta = elRect.bottom - containerRect.bottom + margin;
        }
      }

      if (delta !== 0) {
        container.scrollBy({ top: delta, behavior: 'smooth' });
      }
    }

    function scrollTranslationToCurrent() {
      const list = document.getElementById('translationList');
      const cur = list?.querySelector('.translation-item.is-current');
      if (!list || !cur) return;
      scrollIntoContainer(cur, list, { block: 'center', margin: 16 });
    }

    function scrollTypingCursorIntoView() {
      const wrapper = document.getElementById('typing-wrapper');
      if (!wrapper || !caretEl) return;
      scrollIntoContainer(caretEl, wrapper, { block: 'nearest', margin: 28 });
    }

    // --- Mobile Tab Switching ---
    function switchMobileTab(tab) {
      const dictPanel = document.getElementById('dictationPanel');
      const transPanel = document.getElementById('translationPanel');
      const tabDict = document.getElementById('mobileTabDictation');
      const tabTrans = document.getElementById('mobileTabTranslation');

      if (tab === 'dictation') {
        dictPanel.classList.remove('hidden');
        dictPanel.classList.add('flex');
        transPanel.classList.add('hidden');
        transPanel.classList.remove('flex');

        tabDict.className = 'flex-1 py-1.5 px-3 text-xs font-semibold rounded-lg bg-ink text-white transition-all flex items-center justify-center gap-1.5 cursor-pointer';
        tabTrans.className = 'flex-1 py-1.5 px-3 text-xs font-semibold rounded-lg bg-slate-100 text-ink-secondary hover:bg-slate-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer';
        document.getElementById('typing-engine').focus();
      } else {
        dictPanel.classList.add('hidden');
        dictPanel.classList.remove('flex');
        transPanel.classList.remove('hidden');
        transPanel.classList.add('flex');

        tabTrans.className = 'flex-1 py-1.5 px-3 text-xs font-semibold rounded-lg bg-ink text-white transition-all flex items-center justify-center gap-1.5 cursor-pointer';
        tabDict.className = 'flex-1 py-1.5 px-3 text-xs font-semibold rounded-lg bg-slate-100 text-ink-secondary hover:bg-slate-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer';
        requestAnimationFrame(scrollTranslationToCurrent);
      }
    }

    // --- Render Pipeline ---
    function init() {
      if (!TOTAL) {
        document.getElementById('currentMeaning').textContent = 'Bài viết này chưa có nội dung câu để chép.';
        document.getElementById('typing-engine').innerHTML = '<span class="text-ink-muted text-sm">Không tìm thấy câu nào.</span>';
        return;
      }

      initFxCanvas();
      loadProgressFromStorage();
      updateSoundUI();
      buildTranslationList();
      renderCurrentSentence();
      updateStats();
      updateProgress();
      updateStreakUI(currentStreak);
      setupVocabHighlight();
      setTimeout(() => document.getElementById('typing-engine').focus(), 150);
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
      textEl.textContent = heading.text;
    }

    function buildTranslationList() {
      const list = document.getElementById('translationList');
      if (!list) return;
      let sentenceIndex = 0;
      list.innerHTML = BLOCKS.map((block) => {
        if (block.type === 'heading') {
          return `
            <div class="px-3 py-2 my-2 text-[11px] font-bold text-ink-muted tracking-wide uppercase border-b border-hairline">
              ${escapeHtml(block.text || '')}
            </div>`;
        }
        if (block.type !== 'sentence') return '';
        const i = sentenceIndex++;
        const explainHtml = IS_PRO
          ? `<div class="explain-wrap hidden mt-2.5">
               <button type="button" class="explain-btn inline-flex items-center gap-1.5 text-xs text-amber-800 bg-amber-50/90 border border-amber-200/80 hover:bg-amber-100/90 hover:border-amber-300 font-bold transition-all cursor-pointer py-1.5 px-3 rounded-lg shadow-2xs" onclick="toggleExplainSentence(this, ${i})">
                 <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                 </svg>
                 <span class="btn-text">Giải thích ngữ pháp (AI)</span>
               </button>
               <div class="explain-card hidden mt-2 bg-slate-50 border border-hairline rounded-xl p-3.5 text-xs shadow-xs relative">
                 <div class="flex items-center justify-between pb-2 mb-2.5 border-b border-hairline-subtle">
                   <span class="font-semibold text-accent flex items-center gap-1.5 text-[11px]">
                     <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/></svg>
                     AI Phân tích ngữ pháp & từ vựng
                   </span>
                   <button type="button" onclick="closeExplainCard(this)" class="text-ink-muted hover:text-ink transition-colors cursor-pointer p-0.5 rounded" title="Đóng">✕</button>
                 </div>
                 <div class="explain-content ai-explanation-content"></div>
               </div>
             </div>`
          : '';
        return `
          <div class="translation-item p-3.5 rounded-xl border border-transparent transition-all" data-sentence-index="${i}">
            <div class="flex items-start gap-3">
              <span class="done-marker hidden text-accent text-sm font-bold flex-shrink-0 mt-0.5" aria-hidden="true">✓</span>
              <span class="num-marker text-xs font-bold text-ink-muted w-5 text-right flex-shrink-0 mt-0.5 hud-num">${i + 1}</span>
              <div class="min-w-0 flex-1">
                <p class="sentence-en font-mono text-sm sm:text-[15px] leading-relaxed font-semibold text-ink break-words">${escapeHtml(block.en || '')}</p>
                <p class="sentence-vi text-xs sm:text-sm text-ink-secondary mt-1.5 leading-relaxed">${escapeHtml(block.vi || '')}</p>
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
        const isCurrent = (idx === currentIdx);
        const isDone = (idx < currentIdx) || (idx === currentIdx && isWaitingForNext);

        el.classList.toggle('is-current', isCurrent);
        el.classList.toggle('is-done', isDone);

        if (isCurrent && !isWaitingForNext) {
          el.className = 'translation-item p-3.5 rounded-xl border border-hairline bg-slate-50 is-current';
        } else if (isDone) {
          el.className = 'translation-item p-3.5 rounded-xl border border-emerald-200/60 bg-emerald-50/30 is-done';
        } else {
          el.className = 'translation-item p-3.5 rounded-xl border border-transparent opacity-45 hover:opacity-80 transition-opacity';
        }

        const marker = el.querySelector('.done-marker');
        const numMarker = el.querySelector('.num-marker');
        const explainWrap = el.querySelector('.explain-wrap');
        const sentenceEn = el.querySelector('.sentence-en');

        if (marker) marker.classList.toggle('hidden', !isDone);
        if (numMarker) numMarker.classList.toggle('hidden', isDone);
        if (explainWrap) explainWrap.classList.toggle('hidden', !isDone);
        if (sentenceEn) {
          sentenceEn.classList.toggle('text-accent', isDone);
        }
      });

      const effectiveDone = isWaitingForNext ? currentIdx + 1 : currentIdx;
      const asideCount = document.getElementById('asideDoneCount');
      if (asideCount) asideCount.textContent = Math.min(effectiveDone, TOTAL);
      const mobileDone = document.getElementById('mobileDoneCount');
      if (mobileDone) mobileDone.textContent = Math.min(effectiveDone, TOTAL);

      requestAnimationFrame(scrollTranslationToCurrent);
    }

    function renderCurrentSentence() {
      const sent = currentSentence();
      if (!sent) return;
      isWaitingForNext = false;
      const promptEl = document.getElementById('nextSentencePrompt');
      if (promptEl) {
        promptEl.classList.add('hidden');
        promptEl.classList.remove('flex');
      }
      const engine = document.getElementById('typing-engine');
      if (engine) engine.classList.remove('is-sentence-completed');
      if (caretEl) caretEl.classList.remove('hidden');

      document.getElementById('currentMeaning').textContent = sent.vi || '(Không có nghĩa tiếng Việt)';
      renderChars(sent.en);
      document.getElementById('totalChars').textContent = sent.en.length;
      document.getElementById('charCount').textContent = cursorPos;
      highlightCurrentInPanel();
      updateSectionHeading();
    }

    // Longer runs are emitted as loose characters so an overlong token can still wrap.
    const MAX_UNBREAKABLE_WORD = 22;

    function charSpanHtml(text, i) {
      const ch = text[i];
      const state = i < cursorPos ? charStates[i] : null;
      const stateClass = state === 'wrong' ? 'char-wrong' : state === 'correct' ? 'char-correct' : 'char-ghost';

      if (ch === ' ') {
        const spaceClass = state === 'wrong' ? ' is-space' : '';
        return `<span class="tch tsp ${stateClass}${spaceClass}" data-idx="${i}"> </span>`;
      }
      if (ch === '\n') {
        return `<span class="tch tnl ${stateClass}" data-idx="${i}">↵</span><br>`;
      }
      return `<span class="tch ${stateClass}" data-idx="${i}">${escapeHtml(ch)}</span>`;
    }

    function renderChars(text) {
      const engine = document.getElementById('typing-engine');
      let html = '';
      let i = 0;

      while (i < text.length) {
        const ch = text[i];
        if (ch === '\n') {
          html += charSpanHtml(text, i);
          i++;
          continue;
        }

        if (ch === ' ') {
          html += charSpanHtml(text, i);
          i++;
          continue;
        }

        // Group the word letters
        let wordEnd = i;
        while (wordEnd < text.length && text[wordEnd] !== ' ' && text[wordEnd] !== '\n') wordEnd++;

        let inner = '';
        for (let k = i; k < wordEnd; k++) inner += charSpanHtml(text, k);

        // Include immediately trailing space(s) inside this word block so the next word always starts cleanly at the line start
        let fullEnd = wordEnd;
        while (fullEnd < text.length && text[fullEnd] === ' ') {
          inner += charSpanHtml(text, fullEnd);
          fullEnd++;
        }

        html += (wordEnd - i) > MAX_UNBREAKABLE_WORD
          ? inner
          : `<span class="tw" data-word-start="${i}" data-word-end="${wordEnd - 1}">${inner}</span>`;
        i = fullEnd;
      }

      engine.innerHTML = html + '<span id="typing-caret" class="is-idle"></span>';
      caretEl = document.getElementById('typing-caret');
      if (document.activeElement !== engine) caretEl.classList.add('is-blurred');

      requestAnimationFrame(() => {
        moveCaret(true);
        scrollTypingCursorIntoView();
      });
    }

    function charElAt(idx) {
      return document.querySelector(`#typing-engine [data-idx="${idx}"]`);
    }

    // Repaints a single character instead of rebuilding the line, so animations stay stable.
    function applyCharState(idx, state, animate) {
      const el = charElAt(idx);
      if (!el) return;
      const isSpace = el.classList.contains('tsp');
      const isNewline = el.classList.contains('tnl');
      const stateClass = state === 'wrong' ? 'char-wrong' : state === 'correct' ? 'char-correct' : 'char-ghost';

      el.className = 'tch'
        + (isSpace ? ' tsp' : '')
        + (isNewline ? ' tnl' : '')
        + ' ' + stateClass
        + (state === 'wrong' && isSpace ? ' is-space' : '');

      if (!animate || REDUCED_MOTION) return;
      void el.offsetWidth;
      el.classList.add('is-fresh');
      setTimeout(() => el.classList.remove('is-fresh'), 320);
    }

    // A word that lands fully correct gets a short lift — the smallest unit of "I'm doing well".
    function markWordLocked(idx) {
      if (REDUCED_MOTION) return;
      const word = charElAt(idx)?.closest('.tw');
      if (!word) return;
      const start = parseInt(word.dataset.wordStart, 10);
      const end = parseInt(word.dataset.wordEnd, 10);
      if (idx !== end) return;
      for (let k = start; k <= end; k++) {
        if (charStates[k] !== 'correct') return;
      }
      word.classList.remove('is-locked');
      void word.offsetWidth;
      word.classList.add('is-locked');
      setTimeout(() => word.classList.remove('is-locked'), 340);
    }

    function moveCaret(instant) {
      const engine = document.getElementById('typing-engine');
      const sent = currentSentence();
      if (!caretEl || !engine || !sent) return;

      const len = sent.en.length;
      const atEnd = cursorPos >= len;
      const anchor = charElAt(atEnd ? Math.max(0, len - 1) : cursorPos);
      if (!anchor) return;

      const eRect = engine.getBoundingClientRect();
      const aRect = anchor.getBoundingClientRect();
      const height = Math.round(parseFloat(getComputedStyle(engine).fontSize) * 1.2);

      const clientLeft = engine.clientLeft || 0;
      const clientTop = engine.clientTop || 0;
      const x = (atEnd ? aRect.right : aRect.left) - eRect.left - clientLeft;
      const y = (aRect.top - eRect.top) - clientTop + (aRect.height - height) / 2;

      if (instant) caretEl.style.transition = 'none';
      caretEl.style.height = height + 'px';
      caretEl.style.transform = `translate3d(${Math.round(x)}px, ${Math.round(y)}px, 0)`;
      if (instant) {
        void caretEl.offsetWidth;
        caretEl.style.transition = '';
      }
    }

    // Blinking pauses while keys are flowing, then resumes — reads as momentum, not idle chrome.
    function pulseCaret() {
      if (!caretEl) return;
      caretEl.classList.remove('is-idle');
      clearTimeout(caretIdleTimer);
      caretIdleTimer = setTimeout(() => caretEl && caretEl.classList.add('is-idle'), 500);
    }

    // --- Reward Layer ---
    function spawnRewardChip(text, tone) {
      const layer = document.getElementById('rewardLayer');
      const wrapper = document.getElementById('typing-wrapper');
      if (!layer || !wrapper) return;

      const panelRect = document.getElementById('dictationPanel').getBoundingClientRect();
      const engineRect = document.getElementById('typing-engine').getBoundingClientRect();
      const wrapRect = wrapper.getBoundingClientRect();
      const chip = document.createElement('div');
      chip.className = `reward-chip ${tone} px-3.5 py-1.5 rounded-full text-xs font-extrabold border shadow-xs`;
      chip.textContent = text;
      // Sits in the clear space under the sentence so it never covers what is being read.
      const below = Math.min(engineRect.bottom + 20, wrapRect.bottom - 44);
      chip.style.top = (below - panelRect.top) + 'px';
      layer.appendChild(chip);
      setTimeout(() => chip.remove(), 1300);
    }

    const COMBO_MARKS = [10, 25, 50, 100];

    function comboWindow(streak) {
      let prev = 0;
      for (const mark of COMBO_MARKS) {
        if (streak < mark) return { prev, next: mark };
        prev = mark;
      }
      const next = Math.ceil((streak + 1) / 50) * 50;
      return { prev: next - 50, next };
    }

    function updateComboBar(streak) {
      const fill = document.getElementById('comboFill');
      if (!fill) return;
      const { prev, next } = comboWindow(streak);
      const pct = streak <= 0 ? 0 : ((streak - prev) / (next - prev)) * 100;
      fill.style.width = Math.max(0, Math.min(100, pct)) + '%';
      fill.style.backgroundColor = streak >= 50 ? '#F43F5E'
        : streak >= 30 ? '#8B5CF6'
        : streak >= 15 ? '#F59E0B'
        : '#10B981';
    }

    function announceMilestone(streak) {
      const tone = streak >= 50 ? 'bg-rose-50 text-rose-600 border-rose-200'
        : streak >= 30 ? 'bg-violet-50 text-violet-600 border-violet-200'
        : streak >= 15 ? 'bg-amber-50 text-amber-700 border-amber-200'
        : 'bg-emerald-50 text-emerald-700 border-emerald-200';
      spawnRewardChip(`Chuỗi ${streak} ký tự liên tiếp`, tone);
    }

    // Celebration burst across typing engine when sentence finishes
    function triggerSentenceCompleteEffect() {
      if (!fxCanvas || !fxCtx) return;
      const engine = document.getElementById('typing-engine');
      const panel = document.getElementById('dictationPanel');
      if (!engine || !panel) return;

      const pRect = panel.getBoundingClientRect();
      const eRect = engine.getBoundingClientRect();

      for (let i = 0; i < 28; i++) {
        const x = (eRect.left - pRect.left) + Math.random() * Math.max(eRect.width, 100);
        const y = (eRect.top - pRect.top) + Math.random() * Math.max(eRect.height, 40);
        const angle = -Math.PI / 2 + (Math.random() - 0.5) * 2.2;
        const speed = 2.0 + Math.random() * 3.5;
        fxParticles.push({
          x,
          y,
          vx: Math.cos(angle) * speed,
          vy: Math.sin(angle) * speed - 1.5,
          size: 2.2 + Math.random() * 2.8,
          color: SPARK_COLORS_NORMAL[Math.floor(Math.random() * SPARK_COLORS_NORMAL.length)],
          alpha: 1.0,
          decay: 0.02 + Math.random() * 0.015,
          drag: 0.94,
          gravity: 0.06,
          glow: true,
        });
      }

      if (!isFxLoopRunning) {
        isFxLoopRunning = true;
        requestAnimationFrame(runFxLoop);
      }
    }

    // --- Input & Keystroke Handling ---
    function handleKeydown(e) {
      if (isCompleted || currentIdx >= TOTAL) return;
      const key = e.key;
      const sent = currentSentence();
      if (!sent) return;
      const text = sent.en;

      // Audio shortcut: Ctrl + Space
      if (e.code === 'Space' && (e.ctrlKey || e.metaKey)) {
        e.preventDefault();
        speakCurrentSentence();
        return;
      }

      // If waiting for user to press Enter to advance to next sentence
      if (isWaitingForNext) {
        if (key === 'Enter') {
          e.preventDefault();
          advanceToNextSentence();
        }
        return;
      }

      // Hint shortcut: Tab
      if (key === 'Tab') {
        e.preventDefault();
        hintNextChar();
        return;
      }

      if (!startTime && key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
        startTime = Date.now();
      }

      if (key === 'Backspace') {
        e.preventDefault();
        if (cursorPos > 0) {
          charStates[cursorPos] = null;
          cursorPos--;
          charStates[cursorPos] = null;
          currentStreak = Math.max(0, currentStreak - 1);
          updateStreakUI(currentStreak);
          applyCharState(cursorPos, null, false);
          moveCaret();
          pulseCaret();
          scrollTypingCursorIntoView();
          document.getElementById('charCount').textContent = cursorPos;
          updateStats();
          debouncedSave();
          playKeySound(true, false);
          triggerHaptic(true);
        }
        return;
      }

      if (key === 'Enter' || (key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey)) {
        e.preventDefault();
        if (cursorPos >= text.length) return;
        const expected = text[cursorPos];
        const typed = key === 'Enter' ? '\n' : key;
        const typedIdx = cursorPos;
        totalKeystrokes++;

        const isCorrect = matchesExpected(typed, expected);
        if (isCorrect) {
          totalCorrect++;
          charStates[cursorPos] = 'correct';
          currentStreak++;
          if (currentStreak > maxStreak) maxStreak = currentStreak;
        } else {
          charStates[cursorPos] = 'wrong';
          currentStreak = 0;
          sentenceMistakes++;
        }

        cursorPos++;
        applyCharState(typedIdx, charStates[typedIdx], true);
        if (isCorrect) markWordLocked(typedIdx);
        moveCaret();
        pulseCaret();
        scrollTypingCursorIntoView();
        document.getElementById('charCount').textContent = cursorPos;
        updateStats();
        debouncedSave();

        const isSpaceChar = (typed === ' ' || expected === ' ');
        playKeySound(isCorrect, isSpaceChar);
        triggerHaptic(isCorrect);
        updateStreakUI(currentStreak);

        if (cursorPos >= text.length) {
          finishCurrentSentence(sent, currentIdx);
        }
      }
    }

    // Hint: fills next character
    function hintNextChar() {
      if (isCompleted || currentIdx >= TOTAL || isWaitingForNext) return;
      const sent = currentSentence();
      if (!sent) return;
      const text = sent.en;
      if (cursorPos >= text.length) return;

      const typedIdx = cursorPos;
      totalKeystrokes++;
      totalCorrect++;
      charStates[cursorPos] = 'correct';
      currentStreak++;
      if (currentStreak > maxStreak) maxStreak = currentStreak;
      cursorPos++;
      applyCharState(typedIdx, 'correct', true);
      markWordLocked(typedIdx);
      moveCaret();
      pulseCaret();
      scrollTypingCursorIntoView();
      document.getElementById('charCount').textContent = cursorPos;
      updateStats();
      debouncedSave();

      playKeySound(true, false);
      triggerHaptic(true);
      updateStreakUI(currentStreak);

      if (cursorPos >= text.length) {
        finishCurrentSentence(sent, currentIdx);
      }
    }

    // --- Progression: Sentence Finished (Awaiting Enter) & Advance ---
    function finishCurrentSentence(sent, idx) {
      isWaitingForNext = true;
      playTing();

      const mistakes = sentenceMistakes;
      if (mistakes === 0) {
        spawnRewardChip('Câu hoàn hảo — không lỗi nào', 'bg-emerald-50 text-emerald-700 border-emerald-200');
      } else {
        spawnRewardChip(`Xong câu ${idx + 1} · ${mistakes} lỗi`, 'bg-slate-100 text-ink-secondary border-hairline');
      }

      // Hide caret while waiting for enter
      if (caretEl) caretEl.classList.add('hidden');

      // Green glow effect across entire typing engine
      const engine = document.getElementById('typing-engine');
      if (engine) engine.classList.add('is-sentence-completed');

      // Festive celebration particle burst
      triggerSentenceCompleteEffect();

      // Show the Enter prompt
      const promptEl = document.getElementById('nextSentencePrompt');
      const btnText = document.getElementById('nextSentenceBtnText');
      const hintText = document.getElementById('nextSentenceHintText');
      const isLast = (currentIdx >= TOTAL - 1);

      if (btnText) {
        btnText.textContent = isLast ? 'Xem kết quả' : 'Sang câu tiếp';
      }
      if (hintText) {
        hintText.textContent = isLast ? 'để xem kết quả bài học' : 'để sang câu tiếp theo';
      }
      if (promptEl) {
        promptEl.classList.remove('hidden');
        promptEl.classList.add('flex');
      }

      highlightCurrentInPanel();
      debouncedSave();
    }

    function advanceToNextSentence() {
      if (!isWaitingForNext && cursorPos < (currentSentence()?.en?.length || 0)) return;
      isWaitingForNext = false;

      const promptEl = document.getElementById('nextSentencePrompt');
      if (promptEl) {
        promptEl.classList.add('hidden');
        promptEl.classList.remove('flex');
      }

      const engine = document.getElementById('typing-engine');
      if (engine) engine.classList.remove('is-sentence-completed');
      if (caretEl) caretEl.classList.remove('hidden');

      currentIdx++;
      cursorPos = 0;
      charStates = [];
      sentenceMistakes = 0;

      highlightCurrentInPanel();
      updateProgress();
      updateSectionHeading();
      saveProgressToStorage();

      if (currentIdx >= TOTAL) {
        completeSession();
      } else {
        renderCurrentSentence();
        playStageEnter();
        document.getElementById('typing-engine').focus();
      }
    }

    // Fresh sentence slides in so progression is felt, not just observed.
    function playStageEnter() {
      if (REDUCED_MOTION) return;
      ['currentMeaning', 'typing-engine'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.remove('stage-enter');
        void el.offsetWidth;
        el.classList.add('stage-enter');
        setTimeout(() => el.classList.remove('stage-enter'), 360);
      });
    }

    // --- Stats Calculation ---
    function updateStats() {
      const elapsed = startTime ? (Date.now() - startTime) + accumulatedTimeMs : accumulatedTimeMs;
      const elapsedMin = elapsed / 60000;

      if (elapsedMin > 0) {
        let wordCount = 0;
        for (let i = 0; i < currentIdx; i++) {
          wordCount += (SENTENCES[i].en || '').trim().split(/\s+/).filter(w => w.length > 0).length;
        }
        const partial = (currentSentence()?.en ?? '').substring(0, cursorPos);
        wordCount += partial.trim().split(/\s+/).filter(w => w.length > 0).length;
        const wpm = Math.round(wordCount / elapsedMin);
        document.getElementById('wpmDisplay').textContent = wpm;
      }

      const acc = totalKeystrokes > 0 ? Math.round((totalCorrect / totalKeystrokes) * 100) : 100;
      const accEl = document.getElementById('accDisplay');
      accEl.textContent = acc + '%';

      if (acc >= 95) {
        accEl.className = 'font-bold text-accent hud-num';
      } else if (acc >= 85) {
        accEl.className = 'font-bold text-amber-600 hud-num';
      } else {
        accEl.className = 'font-bold text-error hud-num';
      }
    }

    function updateProgress() {
      const done = currentIdx;
      const pct = TOTAL > 0 ? Math.round((done / TOTAL) * 100) : 0;
      document.getElementById('progressBar').style.width = pct + '%';
      document.getElementById('progressDisplay').textContent = done + ' / ' + TOTAL;
      const label = document.getElementById('sentenceLabel');
      if (label) label.textContent = Math.min(currentIdx + 1, TOTAL);
    }

    // --- Completion ---
    function completeSession() {
      isCompleted = true;
      const elapsed = startTime ? (Date.now() - startTime) + accumulatedTimeMs : accumulatedTimeMs;
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
      document.getElementById('progressDisplay').textContent = TOTAL + ' / ' + TOTAL;

      // Save to server
      fetch(SAVE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ article_id: ARTICLE_ID, wpm, accuracy: acc, completed_sentences: TOTAL }),
      }).catch(() => {});

      clearProgressFromStorage();
      setTimeout(() => document.getElementById('completionModal').classList.add('show'), 400);
    }

    // --- Reset Handling ---
    function confirmResetSession() {
      if (currentIdx === 0 && cursorPos === 0) {
        executeResetSession();
        return;
      }
      document.getElementById('resetConfirmModal').classList.add('show');
    }

    function dismissResetModal() {
      document.getElementById('resetConfirmModal').classList.remove('show');
    }

    function executeResetSession() {
      dismissResetModal();
      currentIdx = 0;
      cursorPos = 0;
      charStates = [];
      totalCorrect = 0;
      totalKeystrokes = 0;
      currentStreak = 0;
      maxStreak = 0;
      sentenceMistakes = 0;
      updateStreakUI(0);
      startTime = null;
      accumulatedTimeMs = 0;
      isCompleted = false;
      isWaitingForNext = false;

      const promptEl = document.getElementById('nextSentencePrompt');
      if (promptEl) {
        promptEl.classList.add('hidden');
        promptEl.classList.remove('flex');
      }
      const engine = document.getElementById('typing-engine');
      if (engine) engine.classList.remove('is-sentence-completed');
      if (caretEl) caretEl.classList.remove('hidden');

      clearProgressFromStorage();
      dismissRestoreBanner();

      document.querySelectorAll('.explain-card').forEach(el => {
        el.classList.add('hidden');
      });
      document.querySelectorAll('.explain-content').forEach(el => {
        el.innerHTML = '';
      });

      document.getElementById('completionModal').classList.remove('show');
      renderCurrentSentence();
      updateStats();
      updateProgress();
      updateSectionHeading();
      document.getElementById('typing-engine').focus();
    }

    // --- AI Explain (Pro) ---
    function renderExplanationMarkdown(text) {
      if (!text) return '';
      if (window.marked && window.DOMPurify) {
        try {
          const parsed = window.marked.parse(text, { breaks: true, gfm: true });
          return window.DOMPurify.sanitize(parsed, { USE_PROFILES: { html: true } });
        } catch (_) {}
      }

      // Robust regex fallback
      let html = escapeHtml(text);
      html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-ink">$1</strong>');
      html = html.replace(/\*(.*?)\*/g, '<em class="text-ink">$1</em>');
      html = html.replace(/`([^`]+)`/g, '<code class="px-1 py-0.5 rounded bg-slate-100 font-mono text-xs text-accent">$1</code>');
      html = html.replace(/^[\*\-]\s+(.+)$/gm, '<li class="ml-4 list-disc text-ink-secondary my-1">$1</li>');
      html = html.replace(/\n\n+/g, '<br><br>').replace(/\n/g, '<br>');
      return html;
    }

    function toggleExplainSentence(btn, idx) {
      const item = btn.closest('.translation-item');
      const card = item.querySelector('.explain-card');
      const content = item.querySelector('.explain-content');

      if (!card.classList.contains('hidden') && content.innerHTML.trim() !== '') {
        card.classList.add('hidden');
        return;
      }

      if (content.innerHTML.trim() !== '') {
        card.classList.remove('hidden');
        return;
      }

      explainSentence(btn, idx);
    }

    function closeExplainCard(closeBtn) {
      const card = closeBtn.closest('.explain-card');
      if (card) card.classList.add('hidden');
    }

    function explainSentence(btn, idx) {
      const sent = SENTENCES[idx];
      if (!sent) return;
      const item = btn.closest('.translation-item');
      const card = item.querySelector('.explain-card');
      const content = item.querySelector('.explain-content');
      const btnText = btn.querySelector('.btn-text') || btn;

      btn.disabled = true;
      const originalText = btnText.textContent;
      btnText.textContent = 'Đang phân tích...';

      card.classList.remove('hidden');
      content.innerHTML = '<div class="flex items-center gap-2 text-ink-muted py-2"><svg class="w-4 h-4 animate-spin text-accent" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><span>Đang phân tích ngữ pháp & từ vựng...</span></div>';

      fetch(EXPLAIN_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ sentence_en: sent.en, sentence_vi: sent.vi }),
      })
      .then(r => r.json())
      .then(data => {
        btn.disabled = false;
        btnText.textContent = originalText;
        if (data.success && data.explanation) {
          content.innerHTML = renderExplanationMarkdown(data.explanation);
        } else {
          content.innerHTML = `<p class="text-error text-xs">${escapeHtml(data.message || 'Không thể giải thích vào lúc này.')}</p>`;
        }
      })
      .catch(() => {
        btn.disabled = false;
        btnText.textContent = originalText;
        content.innerHTML = '<p class="text-error text-xs">Lỗi kết nối. Vui lòng thử lại sau.</p>';
      });
    }

    // --- Vocabulary Highlight-to-Save ---
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
        const idx = parseInt(item.getAttribute('data-sentence-index'), 10);
        const sent = SENTENCES[idx];
        pendingVocab = { word: text, sentence_en: sent?.en || null, sentence_vi: sent?.vi || null };

        try {
          const range = sel.getRangeAt(0);
          const rect = range.getBoundingClientRect();
          vocabTooltip.style.left = (rect.left + rect.width / 2) + 'px';
          vocabTooltip.style.top = (rect.top - 8) + 'px';
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
      vocabSaveBtn.innerHTML = 'Đang lưu...';

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
        vocabSaveBtn.innerHTML = data.status === 'created' ? '✓ Đã lưu từ' : '✓ Đã có trong sổ';
        setTimeout(() => {
          hideVocabTooltip();
          vocabSaveBtn.disabled = false;
          vocabSaveBtn.innerHTML = `
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
            </svg>
            <span>Lưu từ vựng</span>`;
        }, 1200);
      })
      .catch(() => {
        hideVocabTooltip();
        vocabSaveBtn.disabled = false;
        vocabSaveBtn.innerHTML = `
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
          </svg>
          <span>Lưu từ vựng</span>`;
      });
    }

    // --- Event Listeners Binding ---
    function bindEvents() {
      const engine = document.getElementById('typing-engine');
      engine.addEventListener('keydown', handleKeydown);
      engine.addEventListener('click', () => engine.focus());
      document.getElementById('typing-wrapper').addEventListener('click', () => engine.focus());

      engine.addEventListener('focus', () => {
        caretEl?.classList.remove('is-blurred');
        pulseCaret();
      });
      engine.addEventListener('blur', () => caretEl?.classList.add('is-blurred'));

      // Re-wrapping on resize invalidates the caret's cached offsets.
      window.addEventListener('resize', () => moveCaret(true));

      document.getElementById('completionModal').addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('show');
      });
      document.getElementById('resetConfirmModal').addEventListener('click', function (e) {
        if (e.target === this) dismissResetModal();
      });

      engine.addEventListener('paste', e => e.preventDefault());
    }

    document.addEventListener('DOMContentLoaded', () => {
      bindEvents();
      init();
    });
  </script>

  <x-client.layout.ai-chat-widget />
</body>
</html>
