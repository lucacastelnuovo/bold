<?php

use App\Http\Controllers\ApiActivityController;
use App\Http\Controllers\ApiLockController;
use App\Http\Controllers\ApiShareController;
use App\Http\Controllers\ApiUserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->as('v1:')->middleware('auth:sanctum')->group(function (): void {
    Route::get('user', [ApiUserController::class, 'show']);
    Route::post('user/tokens', [ApiUserController::class, 'storeTokens']);

    Route::get('locks', [ApiLockController::class, 'index']);
    Route::post('locks', [ApiLockController::class, 'sync']);
    Route::get('locks/{lock}', [ApiLockController::class, 'show']);
    Route::post('locks/{lock}/activate', [ApiLockController::class, 'activate']);

    Route::post('shares', [ApiShareController::class, 'store']);

    Route::get('activity', [ApiActivityController::class, 'index']);
});
