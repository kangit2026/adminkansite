<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\CareerApplicationController;
use App\Http\Controllers\Api\CareerController;
use App\Http\Controllers\Api\ContactFormController;
use App\Http\Controllers\Api\ContactsController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\InsideCommunityController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\MarketController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\NewsShareController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/settings', [SettingsController::class, 'show']);
Route::get('/about', [AboutController::class, 'show']);
Route::get('/inside-community', [InsideCommunityController::class, 'show']);
Route::get('/market', [MarketController::class, 'show']);
Route::get('/career', [CareerController::class, 'page']);
Route::get('/career/categories', [CareerController::class, 'categories']);
Route::get('/career/vacancies', [CareerController::class, 'vacancies']);
Route::get('/career/directions', [CareerController::class, 'directions']);
Route::get('/career/jobs', [CareerController::class, 'jobs']);
Route::get('/career/jobs/{slug}', [CareerController::class, 'job']);
Route::post('/career/apply', [CareerApplicationController::class, 'store'])->middleware('throttle:career-apply');
Route::get('/contacts', [ContactsController::class, 'show']);
Route::post('/contact', [ContactFormController::class, 'store'])->middleware('throttle:contact-form');
Route::post('/leads/land', [LeadController::class, 'land'])->middleware('throttle:contact-form');
Route::post('/leads/apartment', [LeadController::class, 'apartment'])->middleware('throttle:contact-form');
Route::post('/leads/deck', [LeadController::class, 'deck'])->middleware('throttle:contact-form');
Route::get('/services', [ServiceController::class, 'index']);

// Партнерів на сайті більше немає (2026-11). Порожній список — щоб старий фронтенд
// з блоком партнерів не впав; приберіть маршрут, коли фронтенд перестане його кликати.
Route::get('/Partnership', fn () => response()->json(['success' => true, 'value' => []]));

Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{slug}', [NewsController::class, 'show']);
// «Поділитися новиною» на сайті: лічильник для сторінки «Статистика»; до 20 натискань на хвилину з однієї адреси
Route::post('/news/{slug}/share', [NewsShareController::class, 'store'])->middleware('throttle:20,1');

Route::get('/category', [CategoryController::class, 'index']);
Route::get('/project', [ProjectController::class, 'index']);
Route::get('/project/{slug}', [ProjectController::class, 'show']);
