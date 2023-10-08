<?php

use App\Http\Controllers\DashboardController;
use App\Livewire\LockPanel;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', DashboardController::class)
    ->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->name('dashboard');

Route::get('locks', LockPanel::class)
    ->middleware('signed')
    ->name('locks.index');
