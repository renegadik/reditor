<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedisController;

Route::post('/get_all_keys', [RedisController::class, 'get_all_keys']);

Route::post('/get_key', [RedisController::class, 'get_key']);

Route::post('/create_key', [RedisController::class, 'create_key']);

Route::post('/delete_key', [RedisController::class, 'delete_key']);