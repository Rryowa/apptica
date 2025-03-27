<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppTopCategoryController;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/appTopCategory', [AppTopCategoryController::class, 'getPositions']);

Route::get('/test-redis', function () {
    Cache::put('test_key', 'Hello Redis!', 600); // 10 minutes
    return Cache::get('test_key');
});