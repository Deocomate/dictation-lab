<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\ArticleContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

test('admin can upload article cover image and image url is generated properly', function () {
    Storage::fake('public');

    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);

    $cat = Category::query()->create(['name' => 'Tech', 'slug' => 'tech', 'status' => 'active']);

    $image = UploadedFile::fake()->image('cover.jpg', 600, 400);

    $response = $this->actingAs($admin)->post(route('admin.articles.store'), [
        'title' => 'Article With Image',
        'category_ids' => [$cat->id],
        'status' => 'published',
        'is_premium' => 0,
        'image' => $image,
        'content' => [
            ['type' => 'sentence', 'en' => 'Test sentence.', 'vi' => 'Câu kiểm tra.'],
        ],
    ]);

    $response->assertRedirect(route('admin.articles.index'));

    $article = Article::where('title', 'Article With Image')->first();
    expect($article)->not->toBeNull()
        ->and($article->image_path)->not->toBeNull();

    Storage::disk('public')->assertExists($article->image_path);

    expect($article->imageUrl())->toBe('/storage/'.$article->image_path);

    $indexResponse = $this->actingAs($admin)->get(route('admin.articles.index'));
    $indexResponse->assertOk()
        ->assertSee($article->imageUrl(), false);

    $editResponse = $this->actingAs($admin)->get(route('admin.articles.edit', $article));
    $editResponse->assertOk()
        ->assertSee($article->imageUrl(), false);
});

test('admin can remove existing article cover image', function () {
    Storage::fake('public');

    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);

    $cat = Category::query()->create(['name' => 'Tech', 'slug' => 'tech', 'status' => 'active']);

    $image = UploadedFile::fake()->image('initial.jpg', 600, 400);
    $path = Storage::disk('public')->putFile('articles', $image);

    $article = Article::query()->create([
        'title' => 'Article To Remove Image',
        'image_path' => $path,
        'status' => 'published',
        'is_premium' => false,
        'content_json' => [
            ['type' => 'sentence', 'en' => 'Sentence.', 'vi' => 'Câu.'],
        ],
    ]);
    $article->categories()->attach($cat->id);

    Storage::disk('public')->assertExists($path);

    $response = $this->actingAs($admin)->put(route('admin.articles.update', $article), [
        'title' => 'Article To Remove Image Updated',
        'category_ids' => [$cat->id],
        'status' => 'published',
        'is_premium' => 0,
        'remove_image' => '1',
        'content' => [
            ['type' => 'sentence', 'en' => 'Sentence.', 'vi' => 'Câu.'],
        ],
    ]);

    $response->assertRedirect(route('admin.articles.index'));

    $article->refresh();
    expect($article->image_path)->toBeNull()
        ->and($article->imageUrl())->toBeNull();

    Storage::disk('public')->assertMissing($path);
});
