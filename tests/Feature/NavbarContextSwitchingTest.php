<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest navbar shows separate student and admin login links', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Đăng nhập học viên')
        ->assertSee('Đăng nhập Admin');
});

test('staff navbar shows client and admin context links', function () {
    $user = User::factory()->create([
        'role' => 'superadmin',
        'status' => 'active',
        'subscription_tier' => 'pro',
        'subscription_expires_at' => now()->addYear(),
    ]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Khu học viên')
        ->assertSee('Admin Panel');
});

test('regular user navbar does not show admin panel link', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'subscription_tier' => 'free',
        'subscription_expires_at' => null,
    ]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertDontSee('Admin Panel');
});

test('admin dashboard header links to public site in new tab', function () {
    $user = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Trang chủ', false)
        ->assertSee('Khu học viên', false)
        ->assertSee('target="_blank"', false);
});
