<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppTopCategoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/appTopCategory', [AppTopCategoryController::class, 'getPositions']);
