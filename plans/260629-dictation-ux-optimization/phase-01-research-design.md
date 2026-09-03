---
phase: 1
title: "Research & Design"
status: pending
priority: P1
dependencies: []
---

# Phase 1: Research & Design

## Overview

Phân tích UI hiện tại của Dictation Room, xác nhận pain points, và chốt wireframe + DOM contract trước khi code.

## Requirements

- Functional: Xác định chính xác elements DOM cần giữ/đổi/xóa; map user stories → layout regions.
- Non-functional: Tuân `docs/style-guidelines.md` (desktop-first dictation, semantic colors, spacing 4px grid).

## Architecture

**Luồng thông tin mới:**

```
SENTENCES[] (Blade @json)
    │
    ├─► Dictation Panel
    │     currentMeaning ← SENTENCES[currentIdx].vi  (proximity)
    │     typing-engine  ← renderChars(SENTENCES[currentIdx].en)
    │
    └─► Translation Panel
          translationList ← render all sentences once
          highlight: is-current | is-done | upcoming
```

**So sánh before/after:**

| Vùng cũ | Vùng mới |
|---|---|
| Card article info (ảnh, title, nghĩa) | Header only + nghĩa trong dictation panel |
| Card history | Item `is-done` trong translation panel |
| Card typing | Dictation panel (nghĩa + engine + footer) |
| (không có) | Translation panel (toàn bài) |

## Related Code Files

- Read: `resources/views/client/learning/study-dictation.blade.php`
- Read: `templates/learning/study-dictation.html`
- Read: `docs/style-guidelines.md` §5.1 Dictation
- Reference: `plans/260629-dictation-lab-pivot/plan.md` GĐ3 (layout 3 vùng cũ — superseded bởi plan này)

## Implementation Steps

1. Chụp/screenshot hoặc mô tả layout hiện tại — đo khoảng cách vertical giữa `#currentMeaning` và `#typing-engine`.
2. Liệt kê duplicate UI: header vs card (category, title, sentence count).
3. Chốt typography scale <!-- Updated: Validation Session 1 -->:
   - Engine: **24px desktop / 20px mobile** (user validated)
   - Meaning: `text-2xl lg:text-3xl`
   - Panel items EN: `text-sm font-mono`, VI: `text-xs text-secondary`
4. Vẽ wireframe desktop + mobile (ASCII hoặc mô tả trong plan.md §2).
5. Viết DOM contract (plan.md §3) — review với team trước Phase 2.
6. Xác nhận open questions O1–O3 (mặc định trong plan.md nếu không có phản hồi).

## Success Criteria

- [ ] Wireframe desktop + mobile được ghi trong plan.md §2
- [ ] DOM contract (IDs, classes, JS functions) được liệt kê đầy đủ
- [ ] Typography tokens chốt với lý do (15px → **24px/20px**)
- [ ] Danh sách elements xóa (history card, article card) được approve
- [ ] Không có thay đổi backend trong scope

## Risk Assessment

- **Scope creep (Vite/component hóa):** Pivot plan đã quyết giữ JS inline — không mở rộng.
- **Template parity drift:** Phase 4 phải sync `templates/learning/study-dictation.html` — ghi nhớ sớm.
