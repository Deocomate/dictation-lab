---
phase: 1
title: "Research & UX Spec"
status: pending
priority: P2
dependencies: []
---

# Phase 1: Research & UX Spec

## Overview

Xác nhận touchpoint navbar/auth hiện có và chốt copy + điều kiện hiển thị trước khi sửa Blade.

## Requirements

### Functional
- Guest phân biệt rõ hai luồng đăng nhập (học viên vs admin)
- Staff (`admin`, `superadmin`) chuyển nhanh giữa client dashboard và admin panel không cần gõ URL
- Admin layout có escape hatch ra public site qua tab mới

### Non-functional
- Không thay đổi route, middleware, hoặc auth guard
- Giữ Tailwind CDN + pattern Blade component hiện có
- Responsive: desktop + mobile menu đồng bộ

## Architecture

**Session model (giữ nguyên):**

```
/admin/login  ──► AuthService (any role)
/login        ──► ClientAuthService (role=user only)
                     │
                     ▼
              Single web guard session
                     │
       ┌─────────────┴─────────────┐
  client routes (auth)      admin routes (auth + role)
```

**Điều kiện render navbar CTA:**

| State | Điều kiện | UI |
|-------|-----------|-----|
| Guest | `@guest` | Học viên login + Admin login + Register |
| Client user | `@auth` + `role === 'user'` | Dashboard client + Logout |
| Staff | `@auth` + `isAdmin()` | Khu học viên + Admin Panel + Logout |

## Related Code Files

- Read: `resources/views/components/client/layout/header.blade.php`
- Read: `app/Models/User.php` (`isAdmin()`, `isPro()`)
- Read: `app/Services/Client/ClientAuthService.php`
- Read: `resources/views/components/admin/layout/app.blade.php`
- Read: `resources/views/components/admin/layout/sidebar.blade.php`

## Implementation Steps

1. Audit tất cả layout dùng `<x-client.layout.header />` — xác nhận chỉ một component cần sửa
2. Chốt label tiếng Việt:
   - Guest: `Đăng nhập học viên` | `Đăng nhập Admin`
   - Staff auth: `Khu học viên` | `Admin Panel`
   - Admin escape: `Trang chủ` (icon external optional)
3. Chốt style hierarchy:
   - Primary CTA: `Bắt đầu miễn phí` (guest) / `Đăng xuất` (auth)
   - Secondary ghost: login links
   - Staff dual links: text links, không cạnh tranh với logout button
4. Xác định active state: highlight link theo `request()->routeIs('admin.*')` vs client dashboard routes

## Success Criteria

- [ ] Bảng điều kiện render được team review (không mơ hồ role nào thấy gì)
- [ ] Copy tiếng Việt thống nhất trên desktop/mobile/login pages
- [ ] Xác nhận không cần sửa backend auth trong scope

## Risk Assessment

- **Scope creep** sang impersonation / dual-session → **Loại trừ**; chỉ link switching
- **Navbar overflow mobile** → test trên viewport 375px trong Phase 3
