{{-- SECTION 7: INTERACTIVE DEMO PREVIEW --}}
<section id="demo" class="py-16 sm:py-24 bg-app-bg">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Demo: Bilingual Dictation --}}
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center reveal">
      <div class="order-2 lg:order-1">
        <div class="bg-white rounded-2xl shadow-card border border-border-light p-6 sm:p-8">
          <div class="flex items-center justify-between mb-4 pb-3 border-b border-border-light">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
              </svg>
              <span class="text-sm font-semibold text-text-primary">Chép chính tả song ngữ</span>
            </div>
            <div class="flex items-center gap-3 text-xs text-text-secondary">
              <span>WPM: <strong class="text-text-primary">67</strong></span>
              <span>Chính xác: <strong class="text-brand">96.3%</strong></span>
            </div>
          </div>

          <p class="text-sm text-text-secondary mb-1">Nghĩa tiếng Việt:</p>
          <p class="text-base font-medium text-text-primary mb-4">Nền kinh tế đang tăng trưởng mạnh.</p>

          <div class="font-mono text-base leading-[2] tracking-wide p-4 bg-app-bg rounded-xl border border-border-light">
            <span class="typing-char-correct">The economy is </span><!--
            --><span class="typing-char-wrong">growng</span><!--
            --><span class="typing-cursor-line" aria-hidden="true"></span><!--
            --><span class="typing-char-pending"> rapidly.</span>
          </div>

          <div class="mt-6 bg-gray-100 rounded-full h-2 overflow-hidden">
            <div class="h-full bg-brand rounded-full transition-all duration-300" style="width: 42%"></div>
          </div>
          <p class="text-xs text-text-secondary mt-2">Tiến độ: Câu 2 / 5</p>
        </div>
      </div>

      <div class="order-1 lg:order-2">
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-brand-light rounded-full text-xs font-semibold text-brand uppercase tracking-wide mb-4">Tính năng 1</span>
        <h2 class="text-2xl sm:text-3xl font-bold text-text-primary leading-tight">
          Gõ lại, nhớ ngay.<br />
          <span class="text-brand">Muscle memory cho tiếng Anh.</span>
        </h2>
        <p class="mt-4 text-base text-text-secondary leading-relaxed">
          Nhìn nghĩa tiếng Việt, gõ lại câu tiếng Anh từng ký tự. Sai thì sửa ngay, đúng thì sang câu tiếp —
          não bộ ghi nhớ cấu trúc và từ vựng qua thực hành chủ động.
        </p>
        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 mt-6 text-sm font-medium text-brand hover:text-brand-dark transition-colors duration-200 cursor-pointer group">
          Thử chép chính tả ngay
          <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
          </svg>
        </a>
      </div>
    </div>

    {{-- Demo: Vocabulary + AI --}}
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center mt-20 reveal">
      <div>
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-purple-50 rounded-full text-xs font-semibold text-semantic-purple uppercase tracking-wide mb-4">Tính năng 2 · PRO</span>
        <h2 class="text-2xl sm:text-3xl font-bold text-text-primary leading-tight">
          Bôi đen từ hay, <span class="text-semantic-purple">lưu vào sổ tay.</span>
        </h2>
        <p class="mt-4 text-base text-text-secondary leading-relaxed">
          Trong phòng chép chính tả, bôi đen bất kỳ từ nào để lưu kèm câu ngữ cảnh song ngữ.
          Tài khoản Pro có thêm AI dịch từ và giải thích ngữ pháp câu vừa chép xong.
        </p>
        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 mt-6 text-sm font-medium text-semantic-purple hover:text-purple-700 transition-colors duration-200 cursor-pointer group">
          Khám phá sổ từ vựng
          <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
          </svg>
        </a>
      </div>

      <div>
        <div class="bg-white rounded-2xl shadow-card border border-border-light p-6 sm:p-8">
          <div class="flex items-center gap-2 mb-4 pb-3 border-b border-border-light">
            <svg class="w-4 h-4 text-semantic-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
            <span class="text-sm font-semibold text-text-primary">Sổ từ vựng</span>
          </div>

          <div class="space-y-3">
            <div class="p-4 bg-app-bg rounded-xl border border-border-light">
              <p class="text-sm font-semibold text-brand">rapidly</p>
              <p class="text-xs text-text-secondary mt-1">The economy is growing <span class="font-medium text-text-primary">rapidly</span>.</p>
              <p class="text-xs text-text-secondary mt-0.5">Nền kinh tế đang tăng trưởng mạnh.</p>
              <p class="text-xs text-text-primary mt-2 italic">→ nhanh chóng, mạnh mẽ (adv.)</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-semantic-purple">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>
              AI dịch & giải thích ngữ pháp (Pro)
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
