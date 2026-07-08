<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\NewsTagController;
use App\Http\Controllers\Admin\ProjectCategoryController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(Auth::check() ? 'admin.dashboard' : 'admin.login');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::get('/about', [AboutController::class, 'edit'])->name('about.edit');
        Route::put('/about', [AboutController::class, 'update'])->name('about.update');

        Route::resource('news', NewsController::class)->except(['show']);

        Route::get('/news-tags', [NewsTagController::class, 'index'])->name('news-tags.index');
        Route::post('/news-tags', [NewsTagController::class, 'store'])->name('news-tags.store');
        Route::put('/news-tags/{newsTag}', [NewsTagController::class, 'update'])->name('news-tags.update');
        Route::delete('/news-tags/{newsTag}', [NewsTagController::class, 'destroy'])->name('news-tags.destroy');

        Route::resource('projects', ProjectController::class)->except(['show']);

        Route::get('/project-categories', [ProjectCategoryController::class, 'index'])->name('project-categories.index');
        Route::post('/project-categories', [ProjectCategoryController::class, 'store'])->name('project-categories.store');
        Route::put('/project-categories/{projectCategory}', [ProjectCategoryController::class, 'update'])->name('project-categories.update');
        Route::delete('/project-categories/{projectCategory}', [ProjectCategoryController::class, 'destroy'])->name('project-categories.destroy');
    });
});
