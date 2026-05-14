<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('webhook')->group(function () {
    Route::post('/{token}', [WebhookController::class, 'receiveViaToken']);
    Route::get('/{token}', [WebhookController::class, 'verifyViaToken']);
});
