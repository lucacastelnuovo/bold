<?php

use App\Livewire\Share;
use Illuminate\Support\Facades\Route;

Route::get('/', Share::class)->name('share')
    ->middleware('signed');
