---
phase: 2
title: "Implement Layout & Typography"
status: pending
priority: P1
dependencies: [1]
---

# Phase 2: Implement Layout & Typography

## Overview

Refactor HTML/CSS structure: viewport-filling grid, tăng font typing engine, loại bỏ card thừa và padding dư.

## Requirements

- Functional: Main area chiếm full viewport height; chữ gõ đọc được rõ trên màn 1080p.
- Non-functional: Không break sticky header; `prefers-reduced-motion` giữ nguyên.

## Architecture

```html
<!-- Cấu trúc target (pseudo) -->
<body class="min-h-screen flex flex-col">
  <header class="sticky ... h-14">...</header>
  <main class="flex-1 min-h-0 max-w-7xl mx-auto w-full px-4 lg:px-6
               lg:grid lg:grid-cols-[1fr_380px] lg:gap-4 lg:py-4">
    <section id="dictationPanel" class="flex flex-col min-h-0 ...">
      <!-- meaning + engine + footer -->
    </section>
    <aside id="translationPanel" class="hidden lg:flex flex-col min-h-0 ...">
      <!-- translation list -->
    </aside>
  </main>
</body>
```

## Related Code Files

- Modify: `resources/views/client/learning/study-dictation.blade.php`
  - `<style>` block: `#typing-engine` font-size, line-height, min-height
  - `<main>` structure
  - Xóa block lines ~120-158 (article card + history card)

## Implementation Steps

1. **Xóa card thừa:**
   - Remove article info card (lines 121–147)
   - Remove history section card (lines 149–158)
   - Giữ metadata tối thiểu trong header (đã có title + category)

2. **Refactor `<main>`:**
   ```html
   <main class="flex-1 min-h-0 w-full max-w-7xl mx-auto px-4 sm:px-6
                flex flex-col lg:grid lg:grid-cols-[minmax(0,1fr)_380px] lg:gap-4 py-3 lg:py-4">
   ```

3. **Dictation panel structure:**
   ```html
   <section id="dictationPanel" class="flex flex-col min-h-0 flex-1 bg-white rounded-2xl border ...">
     <div class="px-5 py-3 border-b ... flex justify-between">
       <span class="text-xs font-bold text-text-secondary">Câu <span id="sentenceLabel">1</span> / {{ count }}</span>
       <span class="text-xs text-text-disabled"><span id="charCount">0</span>/<span id="totalChars">0</span></span>
     </div>
     <div class="px-5 sm:px-6 pt-4 pb-2 flex-shrink-0">
       <p id="currentMeaning" class="text-2xl lg:text-3xl font-semibold leading-snug">...</p>
     </div>
     <div id="typing-wrapper" class="flex-1 min-h-0 px-5 sm:px-6 py-3 overflow-y-auto">
       <div id="typing-engine" tabindex="0"></div>
     </div>
     <div class="px-5 py-2.5 border-t ..."><!-- legend compact --></div>
   </section>
   ```

4. **CSS typography update:** <!-- Updated: Validation Session 1 — 24px/20px -->
   ```css
   #typing-engine {
     font-size: 24px;
     line-height: 1.65;
     letter-spacing: 0.02em;
     min-height: 80px;
   }
   @media (max-width: 1023px) {
     #typing-engine { font-size: 20px; line-height: 1.6; }
   }
   .char-cursor::before { height: 1.2em; } /* scale cursor with font */
   ```

5. **Giảm padding toàn cục:**
   - `main`: `py-6 sm:py-8` → `py-3 lg:py-4`
   - Typing wrapper: `py-6 sm:py-8` → `py-3`
   - Bỏ macOS dots hoặc thu nhỏ header card typing

6. **Placeholder translation panel** (empty aside, Phase 3 fill):
   ```html
   <aside id="translationPanel" class="hidden lg:flex ...">...</aside>
   ```

7. **Cập nhật `updateProgress()`** — thêm sync `#sentenceLabel` = `currentIdx + 1`.

## Success Criteria

- [ ] Không còn card article info và history riêng
- [ ] `#typing-engine` font = 24px desktop / 20px mobile (inspect DevTools)
- [ ] `#currentMeaning` nằm trực tiếp trên `#typing-engine`, gap ≤ 16px
- [ ] Main không scroll page khi câu ngắn (<20 ký tự) trên viewport 1080p
- [ ] Header sticky + progress bar vẫn hoạt động
- [ ] Engine gõ vẫn focus và nhận keydown (smoke test thủ công)

## Risk Assessment

- **Flex min-h-0:** Thiếu `min-h-0` trên flex child → overflow không scroll — test câu dài 200+ chars.
- **Regression char cursor:** Cursor `::before height` phải scale theo font mới.
