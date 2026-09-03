---
phase: 3
title: "Test & QA"
status: pending
priority: P2
dependencies: [2]
---

# Phase 3: Test & QA

## Overview

Kiểm tra thủ công và (nếu có) feature test tối thiểu cho điều kiện render navbar theo role.

## Requirements

- Mọi acceptance criteria trong `plan.md` được verify
- Regression: logout, register, mobile menu vẫn hoạt động

## Architecture

Không thêm test infrastructure mới trừ khi repo đã có pattern Pest feature test cho views — ưu tiên manual QA checklist.

## Related Code Files

- Optional create: `tests/Feature/NavbarContextSwitchingTest.php` (chỉ nếu team muốn regression guard)

## Implementation Steps

### Manual QA matrix

| # | Persona | Steps | Expected |
|---|---------|-------|----------|
| 1 | Guest | Mở `/` | Navbar: Đăng nhập học viên + Đăng nhập Admin + Bắt đầu miễn phí |
| 2 | Guest mobile | Mở menu hamburger | Cùng 3 CTA trong dropdown |
| 3 | Superadmin | Login `/admin/login` → mở `/` | Khu học viên + Admin Panel + Đăng xuất |
| 4 | Superadmin | Click Khu học viên | `/dashboard` load, Pro content accessible |
| 5 | Superadmin | Click Admin Panel | `/admin/dashboard` |
| 6 | Superadmin | Từ admin, click Trang chủ | Tab mới mở `/`, session admin vẫn giữ |
| 7 | User | Login `/login` | Chỉ Dashboard client, không có Admin links |
| 8 | Admin role | Login `/admin/login` | Dual links như superadmin |
| 9 | Guest | `/login` footer | Link Đăng nhập Admin hoạt động |
| 10 | Guest | `/admin/login` | Link Về trang chủ + Đăng nhập học viên |

### Automated (optional, khuyến nghị nhẹ)

```php
// tests/Feature/NavbarContextSwitchingTest.php
it('shows dual login links for guests', function () {
    $this->get(route('home'))
        ->assertSee('Đăng nhập học viên')
        ->assertSee('Đăng nhập Admin');
});

it('shows staff context links for superadmin', function () {
    $user = User::factory()->create(['role' => 'superadmin', 'subscription_tier' => 'pro', 'subscription_expires_at' => now()->addYear()]);
    $this->actingAs($user)
        ->get(route('home'))
        ->assertSee('Khu học viên')
        ->assertSee('Admin Panel');
});

it('hides admin links for regular users', function () {
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)
        ->get(route('home'))
        ->assertSee('Dashboard')
        ->assertDontSee('Admin Panel');
});
```

### Lint / build

```bash
php artisan view:clear
./vendor/bin/pint --dirty   # nếu có PHP thay đổi
php artisan test --filter=NavbarContextSwitching  # nếu thêm test
```

## Success Criteria

- [ ] Manual QA matrix 10/10 pass
- [ ] Mobile 375px không overflow navbar CTA
- [ ] Logout từ marketing navbar vẫn `route('logout')` → redirect home
- [ ] Không regression trên trang marketing scroll / mobile menu JS

## Risk Assessment

- Feature test `assertSee` fragile nếu copy đổi — giữ label stable hoặc dùng `data-testid` nếu cần (không bắt buộc scope này)
