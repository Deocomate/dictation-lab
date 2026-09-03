<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createVocabularyArticle(): Article
{
    $category = Category::query()->create([
        'name' => 'Test',
        'slug' => 'test',
        'status' => 'active',
    ]);

    $article = Article::query()->create([
        'title' => 'Test Article',
        'excerpt' => 'Test excerpt',
        'content_json' => [
            ['en' => 'Electricity increased steadily.', 'vi' => 'Điện tăng đều đặn.'],
        ],
        'is_premium' => false,
        'status' => 'published',
    ]);

    $article->categories()->attach($category->id);

    return $article->fresh(['categories']);
}

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('save vocabulary endpoint returns created status for first json request', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'subscription_tier' => 'free',
        'subscription_expires_at' => null,
    ]);

    $article = createVocabularyArticle();

    $response = $this->actingAs($user)->postJson(route('client.vocabulary.store'), [
        'word' => 'steadily',
        'meaning' => 'mot cach on dinh',
        'article_id' => $article->id,
        'sentence_en' => 'Electricity increased steadily.',
        'sentence_vi' => 'Điện tăng đều đặn.',
    ]);

    $response->assertCreated()->assertJson([
        'success' => true,
        'status' => 'created',
        'vocabulary' => [
            'word' => 'steadily',
            'meaning' => 'mot cach on dinh',
        ],
    ]);

    $this->assertDatabaseHas('user_vocabularies', [
        'user_id' => $user->id,
        'word' => 'steadily',
        'meaning' => 'mot cach on dinh',
    ]);
});

test('save vocabulary endpoint returns already_exists without creating duplicates', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'subscription_tier' => 'free',
        'subscription_expires_at' => null,
    ]);

    $article = createVocabularyArticle();

    $this->actingAs($user)->postJson(route('client.vocabulary.store'), [
        'word' => 'steadily',
        'meaning' => 'mot cach on dinh',
        'article_id' => $article->id,
    ])->assertCreated();

    $response = $this->actingAs($user)->postJson(route('client.vocabulary.store'), [
        'word' => 'steadily',
        'meaning' => 'at a regular and even pace',
        'article_id' => $article->id,
    ]);

    $response->assertOk()->assertJson([
        'success' => true,
        'status' => 'already_exists',
    ]);

    $this->assertDatabaseCount('user_vocabularies', 1);
});
