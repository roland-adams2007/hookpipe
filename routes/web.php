<?php

use App\Http\Controllers\WebhookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

// Public routes (anyone can access)
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login.show');
    Route::get('register', [AuthController::class, 'showReg'])->name('reg.show');
    Route::post('login', [AuthController::class, 'verify'])->name('login');
    Route::post('register', [AuthController::class, 'store'])->name('register');
});

Route::post('/token/{token}', [WebhookController::class, 'receive']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::prefix('webhook')->group(function () {
        Route::post('send', [WebhookController::class, 'send']);
        Route::post('echo', [WebhookController::class, 'echo'])->name('webhook.echo');
        Route::get('latest', [WebhookController::class, 'latest']);
        Route::get('verify/{id}', [WebhookController::class, 'verify']);
        Route::get('logs', [WebhookController::class, 'logs'])->name('webhook.logs');
        Route::get('logs/{event}', [WebhookController::class, 'show'])->name('webhook.show');
        Route::delete('logs/{event}', [WebhookController::class, 'destroy'])->name('webhook.destroy');
    });
    Route::get('/token', [TokenController::class, 'show']);
    Route::post('/token', [TokenController::class, 'createOrReplace']);
    Route::delete('/token', [TokenController::class, 'delete']);
});

Route::get('/run-queue', function () {
    Artisan::call('queue:work --once');
    return 'ok';
});