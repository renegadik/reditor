<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedisController;
use App\Http\Controllers\SettingsController;

Route::get('/', [RedisController::class, 'home'])->name('home');

Route::get('/create', [RedisController::class, 'create'])->name('create');

Route::post('/create', [RedisController::class, 'store'])->name('create');

Route::get('/key/{key}', [RedisController::class, 'show'])->name('show');

Route::post('/delete', [RedisController::class, 'delete'])->name('delete');

Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');

Route::post('/update_key', [RedisController::class, 'update_key'])->name('update_key');
Route::post('/delete_subkey', [RedisController::class, 'delete_subkey'])->name('delete_subkey');
Route::post('/add-subkey', [RedisController::class, 'add_subkey'])->name('add_subkey');
