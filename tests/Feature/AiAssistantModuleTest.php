<?php

use App\Models\AiAssistantSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('client chat config endpoint returns public ai assistant settings', function () {
    $response = $this->getJson(route('client.ai-chat.config'));

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.max_questions', 5);
});

test('client chat conversation is capped at five questions', function () {
    AiAssistantSetting::query()->create([
        'is_enabled' => true,
        'system_instruction' => 'You are a concise IELTS Writing assistant.',
        'max_questions' => 5,
        'max_input_chars' => 500,
        'welcome_message' => 'Hello learner.',
    ]);

    Http::fake([
        '*' => Http::response([
            'choices' => [
                ['message' => ['content' => 'AI reply']],
            ],
        ], 200),
    ]);

    $conversationId = 'conv_limit_case';

    for ($index = 1; $index <= 5; $index++) {
        $response = $this->postJson(route('client.ai-chat.message'), [
            'conversation_id' => $conversationId,
            'message' => 'Question '.$index,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('question_count', $index);
    }

    $limitedResponse = $this->postJson(route('client.ai-chat.message'), [
        'conversation_id' => $conversationId,
        'message' => 'Question 6',
    ]);

    $limitedResponse->assertStatus(429)
        ->assertJsonPath('success', false)
        ->assertJsonPath('limit_reached', true)
        ->assertJsonPath('remaining_questions', 0);
});

test('admin can update ai assistant settings and chat model stays decoupled by config', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);

    AiAssistantSetting::query()->create([
        'is_enabled' => true,
        'system_instruction' => 'Current instruction text for AI assistant.',
        'max_questions' => 5,
        'max_input_chars' => 500,
        'welcome_message' => 'Welcome current user.',
    ]);

    $response = $this->actingAs($admin)->put(route('admin.ai-assistant.update'), [
        'is_enabled' => 1,
        'system_instruction' => 'Updated instruction text for AI assistant and user guidance.',
        'max_questions' => 5,
        'max_input_chars' => 450,
        'welcome_message' => 'Xin chào, tôi là trợ lý AI.',
    ]);

    $response->assertRedirect();

    $setting = AiAssistantSetting::query()->first();

    expect($setting)->not->toBeNull()
        ->and($setting->max_questions)->toBe(5)
        ->and($setting->max_input_chars)->toBe(450)
        ->and($setting->welcome_message)->toBe('Xin chào, tôi là trợ lý AI.');
});
