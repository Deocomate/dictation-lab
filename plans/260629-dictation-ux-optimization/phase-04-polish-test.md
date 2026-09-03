---
phase: 4
title: "Polish & Test"
status: pending
priority: P2
dependencies: [3]
---

# Phase 4: Polish & Test

## Overview

Sync template parity, kiểm tra responsive/a11y, regression toàn bộ dictation flow, tinh chỉnh spacing cuối.

## Requirements

- Functional: Toàn bộ user flow dictation hoạt động như trước pivot UX improvements.
- Non-functional: `prefers-reduced-motion`, focus keyboard, contrast đạt baseline style guidelines §8.

## Architecture

Không thay đổi kiến trúc — chỉ QA và micro-adjustments.

## Related Code Files

- Modify: `templates/learning/study-dictation.html` (template parity §9 style-guidelines)
- Verify: `resources/views/client/learning/study-dictation.blade.php`
- Verify: `<x-client.layout.ai-chat-widget />` không che typing area

## Implementation Steps

1. **Template parity:** Port layout/CSS/JS changes sang `templates/learning/study-dictation.html` (structure tương đương, data mock).

2. **Spacing audit:** DevTools measure — không vùng trống >48px giữa meaning và engine; panel không có padding thừa.

3. **Responsive test matrix:**

   | Viewport | Checks |
   |---|---|
   | 1920×1080 | 2 cột, font 24px, no page scroll on short sentence |
   | 1366×768 | Panel 380px không squeeze engine quá hẹp |
   | 1024×768 | Breakpoint lg — 2 cột vừa |
   | 390×844 | Mobile stack, meaning above engine, translation toggle |

4. **Regression checklist (manual):**

   - [ ] Gõ đúng/sai màu brand/red
   - [ ] Backspace sửa
   - [ ] Enter xuống dòng trong câu có `\n`
   - [ ] WPM + accuracy update realtime
   - [ ] Progress bar + sentence counter
   - [ ] Sound toggle + ting on complete
   - [ ] Completion modal + SAVE API (Network tab)
   - [ ] Reset session clears all state
   - [ ] Vocab highlight → save → toast feedback
   - [ ] Pro explain (nếu có account Pro)
   - [ ] Paste blocked on engine
   - [ ] Empty article (`content_json = []`) graceful message

5. **A11y pass:**
   - `#typing-engine` có `tabindex="0"` và focus visible
   - Meaning text readable contrast (primary on white)
   - Toggle translation có `aria-expanded`

6. **Micro-polish (nếu cần sau test):**
   - Giảm legend footer 1 dòng trên mobile
   - `scroll-margin` cho `.is-current` khi auto-scroll
   - Smooth transition khi đổi câu (optional `sentence-slide-in` reuse)

## Success Criteria

- [ ] Template parity file updated
- [ ] Regression checklist 100% pass
- [ ] Responsive matrix pass trên 4 viewport
- [ ] Không console errors khi gõ full bài 10+ câu
- [ ] User pain points ban đầu resolved:
  - [ ] Chữ đủ lớn
  - [ ] Nghĩa ngay trên vùng gõ
  - [ ] 2 side chép/dịch desktop
  - [ ] Không space thừa lớn

## Risk Assessment

- **AI chat widget overlap:** Test với widget mở — adjust `pb` nếu che footer legend.
- **Safari flex bug:** Test `min-h-0` trên Safari iOS nếu target mobile dictation.
