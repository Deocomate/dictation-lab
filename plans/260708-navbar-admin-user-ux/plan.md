---
title: "Navbar Admin-User Context Switching"
description: "Tách entry đăng nhập Admin/Học viên trên navbar client, thêm quick-switch giữa admin và user area, và link Trang chủ (_blank) trên admin layout."
status: pending
priority: P2
effort: ~0.5-1 ngày
branch: main
tags: [navbar, ux, admin, auth, superadmin, blade]
blockedBy: []
blocks: []
created: "2026-07-08"
createdBy: "ck:plan"
source: skill
---

# KẾ HOẠCH: Navbar Admin ↔ User Context Switching

## Tóm tắt

Superadmin (và admin) hiện dùng **một session Laravel duy nhất**: đăng nhập qua `/admin/login` là đủ để truy cập client routes và nội dung Pro (nhờ `subscription_tier = pro` trên seeder). Navbar marketing/client lại chỉ có một nút **Dashboard** trỏ thẳng `admin.dashboard`, không có lối vào khu vực học viên; trang admin cũng không có shortcut ra site public.

Plan này tối ưu UX chuyển ngữ cảnh **không đổi kiến trúc auth** — chỉ bổ sung link/CTA rõ ràng trên Blade components.

---

## Hiện trạng (đã verify code)

| Khu vực | File | Vấn đề |
|---------|------|--------|
| Marketing navbar | `resources/views/components/client/layout/header.blade.php` | Guest: chỉ `Đăng nhập` → `/login`. Auth admin: một link `Dashboard` → `admin.dashboard` |
| Client dashboard sidebar | `resources/views/components/client/layout/partials/sidebar-content.blade.php` | Không có link Admin Panel |
| Admin header | `resources/views/components/admin/layout/app.blade.php` | Không có link ra site public |
| Admin sidebar | `resources/views/components/admin/layout/sidebar.blade.php` | Logo không clickable; không có "Trang chủ" |
| Client login | `resources/views/client/auth/login.blade.php` | Không link sang admin login |
| Admin login | `resources/views/admin/auth/login.blade.php` | Không link sang user login / trang chủ |
| Client auth service | `app/Services/Client/ClientAuthService.php` | `Auth::attempt` bắt `role => user` — superadmin **không** login qua `/login` |

**Quyết định giữ nguyên:** Không mở client login cho superadmin trong scope này. Superadmin đăng nhập admin một lần, chuyển khu vực bằng quick links (cùng session). Tránh phức tạp hóa auth guard.

---

## Mục tiêu (Acceptance Criteria)

### Guest (chưa đăng nhập)
- [ ] Navbar desktop + mobile có **hai entry rõ ràng**:
  - **Đăng nhập học viên** → `route('login')`
  - **Đăng nhập Admin** → `route('admin.auth.login')`
- [ ] Vẫn giữ CTA **Bắt đầu miễn phí** → `route('register')`

### Auth — role `user`
- [ ] Giữ UX hiện tại: `Dashboard` → `client.dashboard`, `Đăng xuất`
- [ ] Không hiện link Admin

### Auth — role `admin` hoặc `superadmin`
- [ ] Navbar hiện **hai shortcut** thay cho một `Dashboard`:
  - **Khu học viên** → `route('client.dashboard')`
  - **Admin Panel** → `route('admin.dashboard')`
- [ ] Mobile menu đồng bộ desktop
- [ ] Client dashboard sidebar thêm **Admin Panel** khi `auth()->user()->isAdmin()`

### Admin layout
- [ ] Header admin có link **Trang chủ** mở tab mới: `target="_blank"` `rel="noopener noreferrer"` → `route('home')`
- [ ] Sidebar admin: logo hoặc mục riêng link **Trang chủ** (`_blank`)
- [ ] (Khuyến nghị) Thêm **Khu học viên** (`client.dashboard`, `_blank`) trên header admin

### Trang đăng nhập
- [ ] `/login`: footer/link "Bạn là quản trị viên? Đăng nhập Admin"
- [ ] `/admin/login`: link "Về trang chủ" + "Đăng nhập học viên"

---

## Phases

| Phase | Name | Status |
|-------|------|--------|
| 1 | [Research & UX spec](./phase-01-research.md) | Pending |
| 2 | [Implement Blade components](./phase-02-implement.md) | Pending |
| 3 | [Test & QA](./phase-03-test.md) | Pending |

## Kiến trúc UX (luồng chuyển ngữ cảnh)

```mermaid
flowchart LR
  subgraph guest [Guest Navbar]
    L1[Đăng nhập học viên]
    L2[Đăng nhập Admin]
  end
  L1 --> CL[/login]
  L2 --> AL[/admin/login]

  subgraph staff [Auth admin/superadmin]
    U[Khu học viên]
    A[Admin Panel]
  end
  U --> CD[/dashboard]
  A --> AD[/admin/dashboard]

  subgraph adminUI [Admin Layout]
    H[Trang chủ _blank]
    H --> HOME[/]
  end
```

## Files dự kiến

| Action | Path |
|--------|------|
| Create | `resources/views/components/client/layout/partials/nav-auth-actions.blade.php` |
| Modify | `resources/views/components/client/layout/header.blade.php` |
| Modify | `resources/views/components/client/layout/partials/sidebar-content.blade.php` |
| Modify | `resources/views/components/admin/layout/app.blade.php` |
| Modify | `resources/views/components/admin/layout/sidebar.blade.php` |
| Modify | `resources/views/client/auth/login.blade.php` |
| Modify | `resources/views/admin/auth/login.blade.php` |

## Rủi ro & giới hạn

| Rủi ro | Mức | Mitigation |
|--------|-----|------------|
| Navbar chật trên mobile khi thêm 2 CTA guest | Thấp | Ẩn label dài trên `sm`, gom vào mobile menu |
| Admin role (không pro) vào client dashboard thấy free tier | Chấp nhận | Đúng hành vi; superadmin vẫn pro qua subscription |
| User thường thấy link admin | Không | Chỉ render khi `isAdmin()` |

## Validation Log

### Verification Results
- Claims checked: 12
- Verified: 12 | Failed: 0 | Unverified: 0
- Tier: Standard
- Failures: none

### Session 1 — 2026-07-08
**Trigger:** User selected `/ck:plan validate` after plan creation
**Questions asked:** 4

#### Questions & Answers

1. **[Scope]** Ai được thấy hai shortcut Khu học viên + Admin Panel trên navbar khi đã đăng nhập?
   - Options: Cả admin và superadmin | Chỉ superadmin | Chỉ khi isAdmin + (superadmin hoặc pro)
   - **Answer:** Cả admin và superadmin đều thấy Khu học viên + Admin Panel
   - **Rationale:** `isAdmin()` là điều kiện duy nhất; admin thường cũng cần preview client

2. **[Architecture]** Link thoát từ admin layout header/sidebar ra phía client?
   - Options: Chỉ Trang chủ _blank | Trang chủ + Khu học viên _blank | Trang chủ _blank, client cùng tab
   - **Answer:** Trang chủ + Khu học viên đều mở tab mới
   - **Rationale:** Giữ admin session/context trong tab hiện tại

3. **[Architecture]** Superadmin có login qua `/login` không?
   - Options: Giữ links only (Recommended) | Mở cho superadmin | Mở cho mọi isAdmin()
   - **Answer:** Giữ nguyên — superadmin login `/admin/login`, chuyển khu bằng quick links
   - **Rationale:** Không đổi ClientAuthService; một session đủ

4. **[UX]** Link Admin Panel trên client dashboard sidebar?
   - Options: _blank | Cùng tab | Bỏ sidebar link
   - **Answer:** Admin Panel mở tab mới
   - **Rationale:** Song song admin + client workflow

#### Confirmed Decisions
- Staff dual links: `isAdmin()` → Khu học viên + Admin Panel (navbar + mobile)
- Admin escape: `route('home')` và `route('client.dashboard')` đều `target="_blank"`
- Auth scope: không sửa `ClientAuthService`
- Client sidebar Admin Panel: `target="_blank"`

#### Action Items
- [x] Cập nhật phase-02 với quyết định validate
- [x] Đóng Open Questions

#### Impact on Phases
- Phase 2: Xác nhận điều kiện `isAdmin()`; admin header có 2 external links
- Phase 3: QA matrix giữ case admin role (#8)

### Whole-Plan Consistency Sweep
- Searched plan + phases for stale open questions, auth changes, link target conflicts
- Result: **0 unresolved contradictions** — ready for `/ck:cook`
