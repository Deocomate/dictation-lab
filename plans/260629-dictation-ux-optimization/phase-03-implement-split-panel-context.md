---
phase: 3
title: "Implement Split Panel & Context"
status: pending
priority: P1
dependencies: [2]
---

# Phase 3: Implement Split Panel & Context

## Overview

Xây translation panel (cột phải desktop), refactor JS để gộp history vào panel, auto-scroll câu hiện tại, mobile collapse.

## Requirements

- Functional: Panel phải hiển thị toàn bộ câu EN+VI; câu đang gõ highlighted; câu done có indicator; nghĩa VI sync realtime.
- Non-functional: Vocab highlight + Pro explain hoạt động trên panel phải; không re-render toàn list mỗi keystroke.

## Architecture

**Render strategy:** Build `#translationList` **một lần** ở `init()`. Mỗi lần đổi câu chỉ toggle classes + scroll — O(n) class toggle, không innerHTML rebuild.

**State classes:**

| Class | Điều kiện | Style gợi ý |
|---|---|---|
| `is-current` | `idx === currentIdx` | `border-l-4 border-brand bg-brand-light/30` |
| `is-done` | `idx < currentIdx` | EN màu brand, opacity 100, icon ✓ |
| (default) | `idx > currentIdx` | `opacity-60` upcoming |

**Mobile `< lg`:** <!-- Updated: Validation Session 1 — collapsed default -->

```html
<div class="lg:hidden mt-3">
  <button id="toggleTranslation" aria-expanded="false" class="text-xs ...">
    📖 Hiện bản dịch (N câu)
  </button>
  <aside id="translationPanelMobile" class="hidden max-h-48 overflow-y-auto ...">
    <!-- shared #translationList — không duplicate DOM -->
  </aside>
</div>
```

**Khuyến nghị KISS:** Dùng **một** `#translationList` — trên mobile panel nằm dưới dictation (không duplicate DOM). CSS: `lg:order-none` vs mobile stack.

## Related Code Files

- Modify: `resources/views/client/learning/study-dictation.blade.php`
  - HTML: `<aside id="translationPanel">` với header + scroll container
  - JS: `buildTranslationList`, `highlightCurrentInPanel`, refactor `pushToHistory`, `setupVocabHighlight`, `resetSession`, `completeSession`

## Implementation Steps

1. **HTML translation panel (desktop):**
   ```html
   <aside id="translationPanel"
          class="hidden lg:flex flex-col min-h-0 bg-white rounded-2xl border border-border-light shadow-card overflow-hidden">
     <div class="px-4 py-3 border-b border-border-light bg-app-bg/50 flex-shrink-0">
       <h3 class="text-xs font-bold text-text-secondary uppercase tracking-wider">Bản dịch</h3>
       <p class="text-[10px] text-text-disabled mt-0.5">Chọn từ để lưu vào sổ từ vựng</p>
     </div>
     <div id="translationList" class="flex-1 min-h-0 overflow-y-auto px-4 py-3 space-y-3"></div>
   </aside>
   ```

2. **CSS panel items:**
   ```css
   .translation-item { padding: 12px; border-radius: 10px; border: 1px solid transparent; transition: background 0.15s; }
   .translation-item.is-current { border-color: #11A683; background: rgba(232,248,243,0.6); border-left-width: 4px; }
   .translation-item.is-done .sentence-en { color: #11A683; }
   .translation-item:not(.is-current):not(.is-done) { opacity: 0.55; }
   ```

3. **JS — `buildTranslationList()`:**
   - Loop `SENTENCES`, tạo `.translation-item[data-sentence-index]`
   - Mỗi item: `.sentence-en` + `.sentence-vi`
   - Pro explain button chỉ render trong item (hidden until `is-done`)

4. **JS — refactor lifecycle:**
   - `init()` → `buildTranslationList()` + `renderCurrentSentence()`
   - `renderCurrentSentence()` → update `#currentMeaning` + `highlightCurrentInPanel()`
   - `onSentenceComplete()` → remove `pushToHistory()` call; chỉ `highlightCurrentInPanel()` + show explain btn on done item
   - `resetSession()` → re-run `highlightCurrentInPanel()`; hide all explain results

5. **JS — `setupVocabHighlight()`:**
   - Change listener target: `#translationList` thay `#historySection`
   - Logic giữ nguyên: mouseup on `.sentence-en` trong `.is-done` item only (hoặc all — test UX)

6. **JS — `explainSentence()`:**
   - `btn.closest('.translation-item')` thay `.sentence-history-item`

7. **Mobile toggle:**
   ```javascript
   document.getElementById('toggleTranslation')?.addEventListener('click', () => {
     document.getElementById('translationPanelMobile')?.classList.toggle('hidden');
   });
   ```
   Panel mobile = same `#translationPanel` visible below dictation on `< lg` (adjust classes: `flex lg:flex` instead of `hidden lg:flex` on mobile stack).

8. **Layout mobile final:**
   - Dictation panel full width
   - Translation panel: `flex lg:hidden` OR shared element with responsive order
   - Default collapsed on mobile via `max-h-0 overflow-hidden` + toggle

9. **Remove dead code:**
   - Delete `pushToHistory()` function entirely
   - Remove `#historyCount` updates in `completeSession`

## Success Criteria

- [ ] Desktop: 2 cột rõ ràng, panel phải scroll độc lập
- [ ] Câu current auto-scroll into view trong panel khi chuyển câu
- [ ] `#currentMeaning` cập nhật đúng mỗi câu, ngay trên engine
- [ ] Vocab highlight-to-save hoạt động trên câu đã done trong panel
- [ ] Pro "Giải thích" hoạt động sau khi hoàn thành câu
- [ ] Mobile: có thể xem bản dịch (toggle hoặc scroll section)
- [ ] Không còn reference tới `#historySection`

## Risk Assessment

- **Double render mobile/desktop:** Tránh 2 list DOM — dùng responsive layout 1 list.
- **Explain button timing:** Chỉ enable sau `is-done` — tránh user explain câu chưa gõ xong.
- **Performance bài dài (50+ câu):** Virtual scroll YAGNI — 50 DOM nodes OK; note nếu >100 câu.
