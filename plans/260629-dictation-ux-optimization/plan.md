---
title: "Tối ưu UI/UX Dictation Room"
description: "Cải thiện trải nghiệm chép chính tả: chữ lớn hơn, layout 2 cột (chép + dịch), nghĩa câu hiện tại ngay trên vùng gõ, tối ưu không gian thừa."
status: in-progress
priority: P1
effort: ~1-2 ngày
branch: main
tags: [ux, dictation, frontend, blade, layout]
blockedBy: [260629-admin-content-optimization]
blocks: []
created: "2026-06-29"
createdBy: "ck:plan"
source: skill
---

# KẾ HOẠCH: Tối ưu UI/UX Dictation Room

> Plan bổ sung cho pivot đã hoàn thành [`260629-dictation-lab-pivot`](../260629-dictation-lab-pivot/plan.md).
> Phạm vi **chỉ frontend** — không đụng controller, API, DB.

---

## 0. BỐI CẢNH & VẤN ĐỀ HIỆN TẠI

File chính: `resources/views/client/learning/study-dictation.blade.php` (~616 dòng, JS inline).

Layout hiện tại là **stack dọc 3 card**:

```
[Header stats]
[Card 1: ảnh + title + nghĩa VI câu hiện tại]   ← xa vùng gõ
[Card 2: lịch sử câu đã hoàn thành]             ← chiếm chỗ khi còn ít câu
[Card 3: typing engine]                          ← chữ 15px, padding lớn
```

| Vấn đề user báo | Nguyên nhân trong code |
|---|---|
| Chữ quá bé | `#typing-engine { font-size: 15px }` (dòng 39) — nhỏ hơn body token 16px trong `docs/style-guidelines.md` |
| Cần 2 side chép/dịch + nghĩa ngay trên khi gõ | Nghĩa VI nằm card riêng phía trên, tách xa typing ~200–400px scroll |
| Space thừa | Trùng metadata (category/title ở header + card), 3 card riêng với `gap-5`, `py-6 sm:py-8`, history `max-h-64` cố định |

Engine JS (`renderCurrentSentence`, `pushToHistory`, vocab highlight) **ổn** — chỉ cần refactor DOM targets, không đổi logic gõ.

---

## 1. MỤC TIÊU UX (ACCEPTANCE)

1. **Typography:** Vùng gõ **24px desktop / 20px mobile** (đã validate), line-height thoáng nhưng không chiếm thêm chiều cao vô ích.
2. **Proximity nghĩa:** Nghĩa tiếng Việt của **câu đang gõ** luôn nằm **ngay phía trên** `#typing-engine`, cách ≤ 16px, không cần scroll.
3. **Split panel (desktop ≥1024px):**
   - **Trái (~60%):** vùng chép (nghĩa + engine + legend gọn).
   - **Phải (~40%):** bản dịch toàn bài — danh sách câu EN+VI, highlight câu hiện tại, câu đã xong có dấu ✓.
4. **Mobile (<1024px):** Stack dọc — vẫn giữ nghĩa ngay trên engine; panel dịch **thu gọn mặc định**, nút toggle "Hiện bản dịch".
5. **Space:** Loại card/article info trùng lặp; main chiếm `100vh - header`; không còn vùng trống lớn khi mới bắt đầu (<5 câu hoàn thành).
6. **Giữ nguyên:** WPM/accuracy, progress, sound toggle, completion modal, vocab highlight-to-save, Pro explain — **không regression**.

---

## 2. THIẾT KẾ LAYOUT MỚI

### 2.1 Wireframe desktop (lg+)

```
┌──────────────────────────────────────────────────────────────────┐
│ ← Back │ Title + category │ WPM │ Acc │ Câu │ 🔇 Reset          │  h-14
│▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ progress bar ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓│
├───────────────────────────────┬──────────────────────────────────┤
│ DICTATION PANEL (flex-1)      │ TRANSLATION PANEL (w-[380px])    │
│ ┌───────────────────────────┐ │ ┌──────────────────────────────┐ │
│ │ Câu 3 / 12                │ │ │ Bản dịch                     │ │
│ │ "Nghĩa tiếng Việt..."     │ │ │ ──────────────────────────── │ │
│ │ (text-2xl, font-semibold) │ │ │ ✓ EN line                    │ │
│ └───────────────────────────┘ │ │   VI line                    │ │
│ ┌───────────────────────────┐ │ │ ▶ EN (current, brand border) │ │
│ │ Ghost typing 20px mono    │ │ │   VI (current)               │ │
│ │                           │ │ │   EN (upcoming, muted)       │ │
│ └───────────────────────────┘ │ │   VI                         │ │
│ Đúng • Sai • Chưa gõ  0/42  │ │ └ scroll ────────────────────┘ │
└───────────────────────────────┴──────────────────────────────────┘
         ↑ min-h-0, overflow hidden — fill viewport
```

### 2.2 Nguyên tắc thiết kế

| Quyết định | Lý do |
|---|---|
| Nghĩa VI **cả trên engine VÀ trong panel phải** | User yêu cầu proximity khi gõ; panel phải cho overview toàn bài |
| Gộp history vào panel phải | Bỏ card "Câu đã hoàn thành" riêng → tiết kiệm ~280px chiều cao |
| Bỏ card article info | Title/category đã có ở header; ảnh thumbnail YAGNI trong room (có thể thêm tooltip nhỏ sau) |
| `h-[calc(100vh-3.5rem-4px)]` cho main | Desktop-first theo style guidelines §7 |
| Giữ JS inline | Nhất quán pivot plan — không Vite |

### 2.3 Typography tokens (dictation-specific)

```css
/* Desktop default — validated 2026-06-29 */
#typing-engine {
  font-size: 24px;
  line-height: 1.65;
  letter-spacing: 0.02em;
  min-height: 0; /* flex child handles height */
}

/* Mobile */
@media (max-width: 1023px) {
  #typing-engine { font-size: 20px; line-height: 1.6; }
}

#currentMeaning {
  /* text-2xl lg:text-3xl, leading-snug */
}

.translation-panel .sentence-current {
  /* border-l-4 border-brand bg-brand-light/40 */
}
```

---

## 3. THAY ĐỔI JS (DOM CONTRACT)

### 3.1 Element IDs mới / đổi tên

| ID / class | Vai trò |
|---|---|
| `#currentMeaning` | Giữ — di chuyển vào dictation panel, ngay trên engine |
| `#translationPanel` | Container panel phải, scroll |
| `#translationList` | Danh sách tất cả câu (render 1 lần lúc init) |
| `.translation-item[data-sentence-index]` | Mỗi câu EN+VI |
| `.translation-item.is-current` | Câu đang gõ |
| `.translation-item.is-done` | Câu đã hoàn thành |

**Xóa:** `#historySection`, `#historyPlaceholder`, `#historyCount` — logic chuyển sang panel phải.

### 3.2 Hàm cần sửa

```javascript
// init() — thêm buildTranslationList()
function buildTranslationList() {
  const list = document.getElementById('translationList');
  list.innerHTML = SENTENCES.map((sent, i) => `
    <div class="translation-item ..." data-sentence-index="${i}">
      <p class="sentence-en ...">${escapeHtml(sent.en)}</p>
      <p class="sentence-vi ...">${escapeHtml(sent.vi || '')}</p>
      ${IS_PRO ? explainBtnHtml(i) : ''}
    </div>
  `).join('');
  highlightCurrentInPanel();
}

function highlightCurrentInPanel() {
  document.querySelectorAll('.translation-item').forEach(el => {
    const idx = +el.dataset.sentenceIndex;
    el.classList.toggle('is-current', idx === currentIdx);
    el.classList.toggle('is-done', idx < currentIdx);
  });
  const cur = document.querySelector('.translation-item.is-current');
  cur?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
}

// renderCurrentSentence() — giữ update #currentMeaning + gọi highlightCurrentInPanel()
// onSentenceComplete() — bỏ pushToHistory(); chỉ highlightCurrentInPanel()
// setupVocabHighlight() — listen trên #translationList thay #historySection
// resetSession() — reset classes is-done/is-current trên panel
```

### 3.3 Không đổi

- `handleKeydown`, `renderChars`, `matchesExpected`, `completeSession`, fetch SAVE/EXPLAIN/VOCAB
- Modal completion, audio toggle

---

## 4. RESPONSIVE

| Breakpoint | Hành vi |
|---|---|
| `lg` (≥1024px) | Grid 2 cột, panel phải cố định ~380px, dictation flex-1 |
| `< lg` | 1 cột: dictation full width; panel dịch **dưới** engine, `max-h-48` scroll, có thể thêm nút toggle "Ẩn/Hiện bản dịch" |
| Header stats | Ẩn trên mobile (đã có pattern hidden sm:flex) — OK |

---

## 5. FILES

| Action | Path |
|---|---|
| **Modify** | `resources/views/client/learning/study-dictation.blade.php` |
| **Modify (parity)** | `templates/learning/study-dictation.html` |
| Không đụng | Controllers, routes, models, migrations |

---

## 6. RỦI RO & MITIGATION

| Rủi ro | Mitigation |
|---|---|
| Panel phải dài → scroll mất context | Auto-scroll câu current; sticky label "Câu X/Y" trên dictation panel |
| Vocab highlight gãy sau đổi DOM | Test mouseup trên `.sentence-en` trong panel phải |
| Explain Pro button di chuyển | Giữ trong `.translation-item.is-done` only |
| AI chat widget che panel | Widget đã floating — verify z-index không che typing focus |
| Font lớn → câu dài tràn | `overflow-y-auto` trên typing wrapper; `word-break: break-word` |

---

## 7. OPEN QUESTIONS

| # | Câu hỏi | Mặc định nếu không phản hồi |
|---|---|---|
| O1 | Panel phải hiện **tất cả** câu (kể cả chưa tới) hay chỉ done + current? | ✅ **Tất cả** — upcoming muted |
| O2 | Mobile: panel dịch mặc định mở hay thu gọn? | ✅ **Thu gọn**, nút toggle |
| O3 | Có cần giữ ảnh thumbnail bài trong room không? | ✅ **Bỏ** |
| O4 | Cỡ chữ vùng gõ? | ✅ **24px desktop / 20px mobile** |

---

## Validation Log

### Verification Results (Session 1 — 2026-06-29)

- **Tier:** Standard (4 phases)
- **Claims checked:** 12
- **Verified:** 12 | **Failed:** 0 | **Unverified:** 0

Key verifications:
- `study-dictation.blade.php:39` — `font-size:15px` confirmed
- `#historySection`, `#currentMeaning`, `#typing-engine` — DOM IDs confirmed
- `pushToHistory()` — exists, to be removed in Phase 3
- `templates/learning/study-dictation.html` — exists for parity
- No Vite/npm — pivot plan confirmed inline JS pattern
- `<x-client.layout.ai-chat-widget />` — present line 613

### User Decisions (Session 1)

| ID | Decision |
|---|---|
| O1 | Panel phải hiện **tất cả câu** (upcoming mờ, current highlight, done ✓) |
| O2 | Mobile panel dịch **thu gọn mặc định**, nút "Hiện bản dịch" |
| O3 | **Bỏ** ảnh thumbnail — title ở header đủ |
| O4 | Font **24px desktop / 20px mobile** |

### Whole-Plan Consistency Sweep

- Updated §1 acceptance, §2.3 typography, §7 open questions, phase-02 CSS steps
- No unresolved contradictions
- **Recommendation:** Proceed to `/ck:cook`

---

## 8. PHASES

| Phase | Name | Deliverable |
|-------|------|-------------|
| 1 | [Research & Design](./phase-01-research-design.md) | Wireframe, DOM contract, typography scale |
| 2 | [Implement Layout & Typography](./phase-02-implement-layout-typography.md) | Grid viewport, font size, bỏ card thừa |
| 3 | [Implement Split Panel & Context](./phase-03-implement-split-panel-context.md) | Panel dịch, proximity nghĩa, JS refactor |
| 4 | [Polish & Test](./phase-04-polish-test.md) | Responsive, a11y, regression checklist |

**Handoff:** `/ck:cook plans/260629-dictation-ux-optimization`
