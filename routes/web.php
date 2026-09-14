<?php

use App\Http\Controllers\Admin\AiAssistantController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\AiChatController;
use App\Http\Controllers\Client\ArticleController as ClientArticleController;
use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\DictationController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('gioi-thieu', [HomeController::class, 'about'])->name('client.home.about');
Route::get('chuc-nang', [HomeController::class, 'features'])->name('client.home.features');
Route::get('goi-lien-he', [HomeController::class, 'pricingAndContact'])->name('client.home.pricing-contact');
Route::post('checkout/sepay/ipn', [CheckoutController::class, 'sepayIpn'])->name('client.checkout.sepay.ipn');

Route::middleware('guest')->group(function () {
    Route::get('login', [ClientAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [ClientAuthController::class, 'login'])->name('login.submit');

    Route::get('auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

    Route::get('register', [ClientAuthController::class, 'showRegister'])->name('register');
    Route::post('register', [ClientAuthController::class, 'register'])->name('register.submit');

    Route::get('forgot-password', [ClientAuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('forgot-password', [ClientAuthController::class, 'forgotPassword'])->name('forgot-password.submit');

    Route::get('reset-password/{token}', [ClientAuthController::class, 'showResetPassword'])->name('reset-password');
    Route::post('reset-password', [ClientAuthController::class, 'resetPassword'])->name('reset-password.submit');
});

Route::get('ai-chat/config', [AiChatController::class, 'config'])
    ->name('client.ai-chat.config');
Route::post('ai-chat/message', [AiChatController::class, 'message'])
    ->middleware('throttle:20,1')
    ->name('client.ai-chat.message');
Route::post('ai-chat/reset', [AiChatController::class, 'reset'])
    ->name('client.ai-chat.reset');

Route::middleware('auth')->group(function () {
    Route::post('logout', [ClientAuthController::class, 'logout'])->name('logout');

    Route::get('dashboard', [ClientDashboardController::class, 'index'])->name('client.dashboard');

    Route::get('dashboard/profile', [ClientDashboardController::class, 'profile'])->name('client.profile');
    Route::put('dashboard/profile', [ClientDashboardController::class, 'updateProfile'])->name('client.profile.update');
    Route::put('dashboard/password', [ClientDashboardController::class, 'updatePassword'])->name('client.password.update');

    Route::get('dashboard/vocabulary', [ClientDashboardController::class, 'vocabulary'])->name('client.vocabulary');
    Route::post('dashboard/vocabulary', [ClientDashboardController::class, 'saveVocabulary'])->name('client.vocabulary.store');
    Route::put('dashboard/vocabulary/{id}', [ClientDashboardController::class, 'updateVocabularyMeaning'])->name('client.vocabulary.update');
    Route::post('dashboard/vocabulary/{id}/translate', [ClientDashboardController::class, 'translateVocabulary'])->name('client.vocabulary.translate');
    Route::delete('dashboard/vocabulary/{id}', [ClientDashboardController::class, 'deleteVocabulary'])->name('client.vocabulary.destroy');

    Route::get('dashboard/billing', [ClientDashboardController::class, 'billing'])->name('client.billing');

    Route::get('articles', [ClientArticleController::class, 'library'])->name('client.articles.library');

    Route::middleware('dictation.limit')->group(function () {
        Route::get('learning/dictation/{article}', [DictationController::class, 'show'])
            ->whereNumber('article')
            ->name('client.learning.dictation');
    });

    Route::post('learning/dictation/save', [DictationController::class, 'saveResult'])->name('client.learning.dictation.save');
    Route::post('learning/dictation/explain', [DictationController::class, 'explain'])
        ->middleware('throttle:20,1')
        ->name('client.learning.dictation.explain');

    Route::get('checkout', [CheckoutController::class, 'index'])->name('client.checkout');
    Route::post('checkout', [CheckoutController::class, 'process'])->name('client.checkout.process');
    Route::get('checkout/{transaction}/success', [CheckoutController::class, 'success'])->name('client.checkout.success');
    Route::get('checkout/{transaction}/failed', [CheckoutController::class, 'failed'])->name('client.checkout.failed');
    Route::get('checkout/{transaction}/pending', [CheckoutController::class, 'pending'])->name('client.checkout.pending');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('auth.login');
        Route::post('login', [AuthController::class, 'login'])->name('auth.login.submit');

        Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('auth.forgot-password');
        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password.submit');

        Route::get('reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('auth.reset-password');
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::middleware('role:superadmin')->group(function () {
            Route::get('users', [UserController::class, 'index'])->name('users.index');
            Route::get('users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('users', [UserController::class, 'store'])->name('users.store');
            Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        Route::middleware('role:superadmin,admin')->group(function () {
            Route::get('/', fn () => redirect()->route('admin.dashboard'))->name('home');
            Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
            Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

            Route::get('ai-assistant', [AiAssistantController::class, 'edit'])->name('ai-assistant.edit');
            Route::put('ai-assistant', [AiAssistantController::class, 'update'])->name('ai-assistant.update');
            Route::get('settings/general', [GeneralSettingController::class, 'edit'])->name('settings.general.edit');
            Route::put('settings/general', [GeneralSettingController::class, 'update'])->name('settings.general.update');

            Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
            Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show');
            Route::put('clients/{client}/status', [ClientController::class, 'updateStatus'])->name('clients.update-status');
            Route::put('clients/{client}/subscription', [ClientController::class, 'updateSubscription'])->name('clients.update-subscription');

            Route::post('categories/quick-store', [CategoryController::class, 'quickStore'])->name('categories.quick-store');
            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('articles', ArticleController::class)->except(['show']);

            Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
            Route::put('transactions/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('transactions.update-status');
            Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

            Route::get('plans', [PlanController::class, 'index'])->name('plans.index');
            Route::get('plans/create', [PlanController::class, 'create'])->name('plans.create');
            Route::post('plans', [PlanController::class, 'store'])->name('plans.store');
            Route::get('plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
            Route::put('plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
            Route::delete('plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');
        });
    });
});
