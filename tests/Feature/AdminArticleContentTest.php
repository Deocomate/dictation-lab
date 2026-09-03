<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\ArticleContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('article content service upgrades legacy blocks and extracts sentences', function () {
    $service = app(ArticleContentService::class);

    $legacy = [
        ['en' => 'Hello.', 'vi' => 'Xin chào.'],
    ];

    $upgraded = $service->upgradeLegacy($legacy);
    expect($upgraded)->toHaveCount(1)
        ->and($upgraded[0]['type'])->toBe('sentence');

    $blocks = [
        ['type' => 'heading', 'level' => 1, 'text' => 'Intro'],
        ['type' => 'sentence', 'en' => 'Hello.', 'vi' => 'Xin chào.'],
        ['type' => 'sentence', 'en' => 'Goodbye.', 'vi' => 'Tạm biệt.'],
    ];

    expect($service->extractSentences($blocks))->toHaveCount(2);
    expect($service->normalizeBlocks($blocks))->toHaveCount(3);
});

test('article sentence count ignores heading blocks', function () {
    $article = Article::query()->create([
        'title' => 'Block test',
        'content_json' => [
            ['type' => 'heading', 'level' => 1, 'text' => 'Section'],
            ['type' => 'sentence', 'en' => 'One.', 'vi' => 'Một.'],
            ['type' => 'sentence', 'en' => 'Two.', 'vi' => 'Hai.'],
        ],
        'is_premium' => false,
        'status' => 'published',
    ]);

    expect($article->sentenceCount())->toBe(2);
});

test('admin can store article with multiple categories and content blocks', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);

    $cats = collect([
        Category::query()->create(['name' => 'Cat A', 'slug' => 'cat-a', 'status' => 'active']),
        Category::query()->create(['name' => 'Cat B', 'slug' => 'cat-b', 'status' => 'active']),
    ]);

    $response = $this->actingAs($admin)->post(route('admin.articles.store'), [
        'title' => 'Test Article',
        'category_ids' => $cats->pluck('id')->all(),
        'status' => 'draft',
        'is_premium' => 0,
        'content' => [
            ['type' => 'heading', 'level' => 1, 'text' => 'Intro'],
            ['type' => 'sentence', 'en' => 'Hello.', 'vi' => 'Xin chào.'],
        ],
    ]);

    $response->assertRedirect(route('admin.articles.index'));

    $article = Article::first();
    expect($article)->not->toBeNull()
        ->and($article->categories)->toHaveCount(2)
        ->and($article->sentenceCount())->toBe(1)
        ->and($article->content_json[0]['type'])->toBe('heading');
});

test('client library filters articles by category slug', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
    ]);

    $business = Category::query()->create(['name' => 'Business', 'slug' => 'business', 'status' => 'active']);
    $travel = Category::query()->create(['name' => 'Travel', 'slug' => 'travel', 'status' => 'active']);

    $businessArticle = Article::query()->create([
        'title' => 'Business Article',
        'content_json' => [['type' => 'sentence', 'en' => 'Biz.', 'vi' => 'Kinh doanh.']],
        'is_premium' => false,
        'status' => 'published',
    ]);
    $businessArticle->categories()->attach($business->id);

    $travelArticle = Article::query()->create([
        'title' => 'Travel Article',
        'content_json' => [['type' => 'sentence', 'en' => 'Trip.', 'vi' => 'Du lịch.']],
        'is_premium' => false,
        'status' => 'published',
    ]);
    $travelArticle->categories()->attach($travel->id);

    $response = $this->actingAs($user)->get(route('client.articles.library', ['category' => 'business']));

    $response->assertOk();
    $response->assertSee('Business Article');
    $response->assertDontSee('Travel Article');
});
