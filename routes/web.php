<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedisController;

Route::get('/', [RedisController::class, 'home'])->name('home');

Route::get('/create', [RedisController::class, 'create'])->name('create');

Route::post('/create', [RedisController::class, 'store'])->name('create');

Route::get('/key/{key}', [RedisController::class, 'show'])->name('show');

Route::post('/delete', [RedisController::class, 'delete'])->name('delete');

