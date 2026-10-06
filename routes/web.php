<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\ContactSubmissionController;
use App\Http\Controllers\Admin\ContactsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InsideCommunityBlockController;
use App\Http\Controllers\Admin\MarketController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\NewsTagController;
use App\Http\Controllers\Admin\ProjectCategoryController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\InsideCommunityPageController;
use App\Http\Controllers\Admin\ListingSeoSettingsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SiteFooterSettingsController;
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

        Route::get('/inside-community', [InsideCommunityPageController::class, 'edit'])->name('inside-community.edit');
        Route::put('/inside-community', [InsideCommunityPageController::class, 'update'])->name('inside-community.update');
        Route::resource('inside-community/blocks', InsideCommunityBlockController::class)
            ->except(['show'])
            ->names('inside-community.blocks')
            ->parameters(['blocks' => 'block']);

        Route::get('news/seo', [ListingSeoSettingsController::class, 'edit'])->defaults('section', 'news')->name('news.seo.edit');
        Route::put('news/seo', [ListingSeoSettingsController::class, 'update'])->defaults('section', 'news')->name('news.seo.update');
        Route::resource('news', NewsController::class)->except(['show']);

        Route::get('/news-tags', [NewsTagController::class, 'index'])->name('news-tags.index');
        Route::post('/news-tags', [NewsTagController::class, 'store'])->name('news-tags.store');
        Route::put('/news-tags/{newsTag}', [NewsTagController::class, 'update'])->name('news-tags.update');
        Route::delete('/news-tags/{newsTag}', [NewsTagController::class, 'destroy'])->name('news-tags.destroy');

        Route::get('projects/seo', [ListingSeoSettingsController::class, 'edit'])->defaults('section', 'projects')->name('projects.seo.edit');
        Route::put('projects/seo', [ListingSeoSettingsController::class, 'update'])->defaults('section', 'projects')->name('projects.seo.update');
        Route::resource('projects', ProjectController::class)->except(['show']);

        Route::get('/project-categories', [ProjectCategoryController::class, 'index'])->name('project-categories.index');
        Route::post('/project-categories', [ProjectCategoryController::class, 'store'])->name('project-categories.store');
        Route::put('/project-categories/{projectCategory}', [ProjectCategoryController::class, 'update'])->name('project-categories.update');
        Route::delete('/project-categories/{projectCategory}', [ProjectCategoryController::class, 'destroy'])->name('project-categories.destroy');

        Route::get('services/seo', [ListingSeoSettingsController::class, 'edit'])->defaults('section', 'services')->name('services.seo.edit');
        Route::put('services/seo', [ListingSeoSettingsController::class, 'update'])->defaults('section', 'services')->name('services.seo.update');
        Route::resource('services', ServiceController::class)->except(['show']);

        Route::get('/market', [MarketController::class, 'edit'])->name('market.edit');
        Route::put('/market', [MarketController::class, 'update'])->name('market.update');

        Route::get('/career', [CareerController::class, 'edit'])->name('career.edit');
        Route::put('/career', [CareerController::class, 'update'])->name('career.update');

        Route::get('/contacts/site', [SiteFooterSettingsController::class, 'edit'])->name('contacts.site.edit');
        Route::put('/contacts/site', [SiteFooterSettingsController::class, 'update'])->name('contacts.site.update');
        Route::get('/contacts', [ContactsController::class, 'edit'])->name('contacts.edit');
        Route::put('/contacts', [ContactsController::class, 'update'])->name('contacts.update');

        Route::get('/contact-submissions', [ContactSubmissionController::class, 'index'])->name('contact-submissions.index');
        Route::put('/contact-submissions/{contactSubmission}', [ContactSubmissionController::class, 'update'])->name('contact-submissions.update');
    });
});
