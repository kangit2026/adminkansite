<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/settings', [SettingsController::class, 'show']);
Route::get('/about', [AboutController::class, 'show']);
Route::get('/services', [ServiceController::class, 'index']);

Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{slug}', [NewsController::class, 'show']);

Route::get('/category', [CategoryController::class, 'index']);
Route::get('/project', [ProjectController::class, 'index']);
Route::get('/project/{slug}', [ProjectController::class, 'show']);
