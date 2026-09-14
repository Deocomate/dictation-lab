<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view dictation learning page for published article', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
    ]);

    $category = Category::query()->create([
        'name' => 'Environment',
        'slug' => 'environment',
        'status' => 'active',
    ]);

    $article = Article::query()->create([
        'title' => 'Crop-growing Skyscrapers',
        'slug' => 'crop-growing-skyscrapers',
        'content_json' => [
            ['type' => 'sentence', 'en' => 'Vertical farming is revolutionary.', 'vi' => 'Nông nghiệp thẳng đứng mang tính cách mạng.'],
            ['type' => 'sentence', 'en' => 'It saves significant land and water.', 'vi' => 'Nó tiết kiệm đáng kể đất đai và nước.'],
        ],
        'status' => 'published',
        'is_premium' => false,
    ]);
    $article->categories()->attach($category->id);

    $response = $this->actingAs($user)->get(route('client.learning.dictation', $article));

    $response->assertStatus(200);
    $response->assertSee('Crop-growing Skyscrapers');
    $response->assertSee('Vertical farming is revolutionary.');
    $response->assertSee('Nông nghiệp thẳng đứng mang tính cách mạng.');
    $response->assertSee('Environment');
    $response->assertSee('dictation_session_');
});

test('free user cannot access premium article dictation', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'subscription_tier' => 'free',
        'status' => 'active',
    ]);

    $premiumArticle = Article::query()->create([
        'title' => 'Advanced IELTS 9.0 Dictation',
        'slug' => 'advanced-ielts-dictation',
        'content_json' => [
            ['type' => 'sentence', 'en' => 'Sophisticated lexical resource.', 'vi' => 'Vốn từ vựng nâng cao.'],
        ],
        'status' => 'published',
        'is_premium' => true,
    ]);

    $response = $this->actingAs($user)->get(route('client.learning.dictation', $premiumArticle));

    $response->assertStatus(403);
});

test('user can save dictation completion result', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
    ]);

    $article = Article::query()->create([
        'title' => 'Sample Article',
        'slug' => 'sample-article',
        'content_json' => [
            ['type' => 'sentence', 'en' => 'Hello world.', 'vi' => 'Chào thế giới.'],
        ],
        'status' => 'published',
        'is_premium' => false,
    ]);

    $response = $this->actingAs($user)->postJson(route('client.learning.dictation.save'), [
        'article_id' => $article->id,
        'wpm' => 58,
        'accuracy' => 96.5,
        'completed_sentences' => 1,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('dictation_histories', [
        'user_id' => $user->id,
        'article_id' => $article->id,
        'wpm' => 58,
        'completed_sentences' => 1,
    ]);
});
