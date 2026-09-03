---
phase: 2
title: "Implement Blade Components"
status: pending
priority: P1
dependencies: [1]
---

# Phase 2: Implement Blade Components

## Overview

Tách logic CTA navbar vào partial dùng chung, cập nhật header client, sidebar client/admin, và cross-links trên trang login.

<!-- Updated: Validation Session 1 — isAdmin() dual links; admin escape home+client _blank; no ClientAuthService change; sidebar admin _blank -->

## Requirements

- DRY: desktop + mobile navbar dùng chung partial
- `@auth` / `@guest` / `isAdmin()` là single source of truth cho điều kiện
- Admin external links: `target="_blank"` + `rel="noopener noreferrer"`

## Architecture

```
header.blade.php
  └── @include nav-auth-actions (desktop CTA)
  └── mobile menu @include nav-auth-actions (mobile variant)

nav-auth-actions.blade.php
  @guest → dual login + register
  @auth + isAdmin() → dual dashboard links + logout
  @auth + !isAdmin() → client dashboard + logout
```

## Related Code Files

- Create: `resources/views/components/client/layout/partials/nav-auth-actions.blade.php`
- Modify: `resources/views/components/client/layout/header.blade.php`
- Modify: `resources/views/components/client/layout/partials/sidebar-content.blade.php`
- Modify: `resources/views/components/admin/layout/app.blade.php`
- Modify: `resources/views/components/admin/layout/sidebar.blade.php`
- Modify: `resources/views/client/auth/login.blade.php`
- Modify: `resources/views/admin/auth/login.blade.php`
- Modify: `resources/views/components/admin/layout/auth.blade.php` (optional footer link trang chủ)

## Implementation Steps

### Step 1 — Tạo partial `nav-auth-actions.blade.php`

```blade
@props(['variant' => 'desktop']) {{-- desktop | mobile --}}

@php
  $linkClass = $variant === 'mobile'
    ? 'block text-sm font-medium text-text-secondary hover:text-text-primary transition-colors cursor-pointer'
    : 'text-sm font-medium text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer';
  $user = auth()->user();
@endphp

@guest
  <a href="{{ route('login') }}" class="{{ $linkClass }} {{ $variant === 'desktop' ? 'hidden sm:inline-block' : '' }}">
    Đăng nhập học viên
  </a>
  <a href="{{ route('admin.auth.login') }}" class="{{ $linkClass }}">
    Đăng nhập Admin
  </a>
  {{-- Register CTA: giữ button brand như hiện tại --}}
@else
  @if($user->isAdmin())
    <a href="{{ route('client.dashboard') }}" class="{{ $linkClass }}">Khu học viên</a>
    <a href="{{ route('admin.dashboard') }}" class="{{ $linkClass }}">Admin Panel</a>
  @else
    <a href="{{ route('client.dashboard') }}" class="{{ $linkClass }} {{ $variant === 'desktop' ? 'hidden sm:inline-block' : '' }}">Dashboard</a>
  @endif
  {{-- Logout form: giữ nguyên route('logout') --}}
@endguest
```

> Điều chỉnh class/layout khi implement để khớp spacing hiện có; trên mobile có thể rút gọn label `Admin` nếu cần.

### Step 2 — Refactor `header.blade.php`

1. Thay block `@auth` / `@else` desktop CTA bằng `@include('components.client.layout.partials.nav-auth-actions', ['variant' => 'desktop'])`
2. Thay block mobile menu tương tự với `variant => 'mobile'`
3. Giữ nút hamburger và nav links marketing không đổi

### Step 3 — Client dashboard sidebar

Trong `sidebar-content.blade.php`, sau nav chính (trước profile block):

```blade
@if(auth()->user()->isAdmin())
  <div class="pt-2 border-t border-border-light mt-2">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-text-secondary cursor-pointer" target="_blank" rel="noopener noreferrer">
      {{-- shield icon --}}
      Admin Panel
    </a>
  </div>
@endif
```

### Step 4 — Admin layout header (`app.blade.php`)

**Validated:** Cả hai link mở tab mới (`_blank`).

Trong `<header>` bên phải (cạnh date):

```blade
<a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
   class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-brand transition-colors">
  Trang chủ
</a>
<a href="{{ route('client.dashboard') }}" target="_blank" rel="noopener noreferrer"
   class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-brand transition-colors">
  Khu học viên
</a>
```

### Step 5 — Admin sidebar

Bọc logo block bằng `<a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">` và/hoặc thêm mục **Khu học viên** (`client.dashboard`, `_blank`) trong footer sidebar gần profile.

### Step 6 — Login pages cross-links

**`client/auth/login.blade.php`** — dưới form card:

```blade
<p class="text-center text-sm text-text-secondary mt-4">
  Bạn là quản trị viên?
  <a href="{{ route('admin.auth.login') }}" class="text-brand font-semibold hover:text-brand-dark">Đăng nhập Admin</a>
</p>
```

**`admin/auth/login.blade.php`** — sau form:

```blade
<div class="mt-4 text-center space-y-2 text-sm text-text-secondary">
  <p><a href="{{ route('home') }}" class="text-brand hover:text-brand-dark">← Về trang chủ</a></p>
  <p>Học viên? <a href="{{ route('login') }}" class="text-brand font-semibold hover:text-brand-dark">Đăng nhập học viên</a></p>
</div>
```

### Step 7 — Active link highlight (optional polish)

Trong header staff links, thêm class active khi:
- `request()->routeIs('admin.*')` → highlight Admin Panel
- `request()->routeIs('client.dashboard', 'client.articles.*', ...)` → highlight Khu học viên

## Success Criteria

- [ ] Guest thấy 2 login entry trên desktop + mobile
- [ ] Superadmin đăng nhập admin → navbar marketing hiện cả Khu học viên + Admin Panel
- [ ] User thường không thấy link admin
- [ ] Admin header có Trang chủ mở tab mới
- [ ] Không duplicate logout form
- [ ] `php artisan view:cache` pass (nếu dùng trong CI)

## Risk Assessment

| Risk | Mitigation |
|------|------------|
| Partial path sai khi include | Dùng `@include` với path đầy đủ đã test trong project |
| `_blank` trên sidebar client admin link | **Validated:** Admin Panel `_blank` từ client sidebar; marketing navbar Khu học viên same tab |
