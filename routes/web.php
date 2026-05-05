<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Trader\DashboardController;
use App\Http\Controllers\Trader\SignalController;
use App\Http\Controllers\Trader\SubscriptionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TradeController;
use App\Http\Controllers\Admin\ApiKeyController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CmsController;
use Illuminate\Support\Facades\Route;

// Landing
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/terms', [LandingController::class, 'terms'])->name('terms');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');

// Auth routes (from Breeze - keep existing)
require __DIR__.'/auth.php';

// Trader routes
Route::middleware(['auth', 'isActive', 'verified'])->group(function () {
    // Subscription plans (public to authenticated users)
    Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::post('/subscription/initialize', [SubscriptionController::class, 'initialize'])->name('subscription.initialize');
    Route::get('/subscription/callback', [SubscriptionController::class, 'callback'])->name('subscription.callback');
    Route::post('/subscription/webhook', [SubscriptionController::class, 'webhook'])->name('subscription.webhook')->withoutMiddleware(['auth']);
    Route::get('/subscription/status', [SubscriptionController::class, 'status'])->name('subscription.status');

    // Protected trader area
    Route::middleware(['isSubscribed'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/signals', [SignalController::class, 'index'])->name('signals.index');
        Route::get('/signals/{trade}', [SignalController::class, 'show'])->name('signals.show');
        Route::get('/performance', [DashboardController::class, 'performance'])->name('performance');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'isActive', 'isAdmin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Trades
    Route::get('/trades', [TradeController::class, 'index'])->name('trades.index');
    Route::get('/trades/create', [TradeController::class, 'create'])->name('trades.create');
    Route::post('/trades', [TradeController::class, 'store'])->name('trades.store');
    Route::get('/trades/{trade}/edit', [TradeController::class, 'edit'])->name('trades.edit');
    Route::put('/trades/{trade}', [TradeController::class, 'update'])->name('trades.update');
    Route::delete('/trades/{trade}', [TradeController::class, 'destroy'])->name('trades.destroy');
    Route::post('/trades/generate', [TradeController::class, 'generate'])->name('trades.generate');

    // API Keys
    Route::get('/api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('/api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::put('/api-keys/{apiKey}', [ApiKeyController::class, 'update'])->name('api-keys.update');
    Route::delete('/api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');
    Route::patch('/api-keys/{apiKey}/toggle', [ApiKeyController::class, 'toggle'])->name('api-keys.toggle');

    // Settings & CMS
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/cms', [CmsController::class, 'index'])->name('cms.index');
    Route::post('/cms', [CmsController::class, 'update'])->name('cms.update');

    // Guide
    Route::get('/guide', [AdminDashboardController::class, 'guide'])->name('guide');
});
