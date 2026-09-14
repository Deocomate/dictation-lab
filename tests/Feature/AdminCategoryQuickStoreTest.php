<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can quick store category with json response', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);

    $response = $this->actingAs($admin)->postJson(route('admin.categories.quick-store'), [
        'name' => 'Công Nghệ Mới',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'category' => [
                'name' => 'Công Nghệ Mới',
                'slug' => 'cong-nghe-moi',
            ],
        ]);

    $this->assertDatabaseHas('categories', [
        'name' => 'Công Nghệ Mới',
        'slug' => 'cong-nghe-moi',
        'status' => 'active',
    ]);
});

test('quick store automatically disambiguates duplicate slugs', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);

    Category::query()->create([
        'name' => 'Du Lịch',
        'slug' => 'du-lich',
        'status' => 'active',
    ]);

    $response = $this->actingAs($admin)->postJson(route('admin.categories.quick-store'), [
        'name' => 'Du Lịch',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'category' => [
                'name' => 'Du Lịch',
                'slug' => 'du-lich-1',
            ],
        ]);

    $this->assertDatabaseHas('categories', [
        'slug' => 'du-lich-1',
    ]);
});

test('quick store requires valid name', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);

    $response = $this->actingAs($admin)->postJson(route('admin.categories.quick-store'), [
        'name' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('regular user cannot quick store category', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->postJson(route('admin.categories.quick-store'), [
        'name' => 'Hacker Category',
    ]);

    $response->assertStatus(403);
});

test('admin can create article with category created via quick store', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);

    $quickResponse = $this->actingAs($admin)->postJson(route('admin.categories.quick-store'), [
        'name' => 'IELTS Reading',
    ]);

    $categoryId = $quickResponse->json('category.id');

    $articleResponse = $this->actingAs($admin)->post(route('admin.articles.store'), [
        'title' => 'Bài đọc IELTS số 1',
        'category_ids' => [$categoryId],
        'status' => 'published',
        'is_premium' => '1',
        'content' => [
            ['type' => 'sentence', 'en' => 'Artificial intelligence is evolving rapidly.', 'vi' => 'Trí tuệ nhân tạo đang phát triển nhanh chóng.'],
        ],
    ]);

    $articleResponse->assertRedirect(route('admin.articles.index'));

    $article = Article::query()->where('title', 'Bài đọc IELTS số 1')->first();
    expect($article)->not->toBeNull();
    expect($article->categories->pluck('id')->all())->toContain($categoryId);
});
