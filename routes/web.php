<?php

use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\CareerDirectionController;
use App\Http\Controllers\Admin\ContactSubmissionController;
use App\Http\Controllers\Admin\ContactsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DevLoginController;
use App\Http\Controllers\Admin\HrSettingsController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\NewsTagController;
use App\Http\Controllers\Admin\ProjectCategoryController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ListingSeoSettingsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SiteFooterSettingsController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VacancyController;
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

    // тестовий вхід одним кліком — лише локально (див. DevLoginController::enabled), інакше 404
    Route::post('/dev-login/{role}', [DevLoginController::class, 'store'])->name('dev-login');

    // HR бачить лише цей блок; адміністратор — усе. З дашборду HR потрапляє до кандидатів.
    Route::middleware(['auth', 'role:admin|hr'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('vacancies', VacancyController::class)->except(['show']);
        Route::post('/vacancies/{vacancy}/publish', [VacancyController::class, 'publish'])->name('vacancies.publish');
        Route::post('/vacancies/{vacancy}/close', [VacancyController::class, 'close'])->name('vacancies.close');
        Route::post('/vacancies/{vacancy}/reopen', [VacancyController::class, 'reopen'])->name('vacancies.reopen');

        Route::get('/candidates/export', [CandidateController::class, 'export'])->name('candidates.export');
        Route::resource('candidates', CandidateController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::get('/candidates/{candidate}/cv', [CandidateController::class, 'cv'])->name('candidates.cv');
        Route::post('/candidates/{candidate}/notes', [CandidateController::class, 'storeNote'])->name('candidates.notes.store');

        Route::get('/hr-settings', [HrSettingsController::class, 'edit'])->name('hr-settings.edit');
        Route::put('/hr-settings', [HrSettingsController::class, 'update'])->name('hr-settings.update');
        Route::post('/career-directions', [CareerDirectionController::class, 'store'])->name('career-directions.store');
        Route::put('/career-directions/{careerDirection}', [CareerDirectionController::class, 'update'])->name('career-directions.update');
        Route::delete('/career-directions/{careerDirection}', [CareerDirectionController::class, 'destroy'])->name('career-directions.destroy');
    });

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');

        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}/password', [UserController::class, 'password'])->name('users.password');
        Route::put('/users/{user}/active', [UserController::class, 'toggle'])->name('users.toggle');

        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

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

        Route::get('/footer', [SiteFooterSettingsController::class, 'edit'])->name('footer.edit');
        Route::put('/footer', [SiteFooterSettingsController::class, 'update'])->name('footer.update');
        Route::redirect('/contacts/site', '/admin/footer'); // стара адреса, коли футер був вкладкою «Контактів»
        Route::get('/contacts', [ContactsController::class, 'edit'])->name('contacts.edit');
        Route::put('/contacts', [ContactsController::class, 'update'])->name('contacts.update');

        Route::get('/contact-submissions', [ContactSubmissionController::class, 'index'])->name('contact-submissions.index');
        Route::put('/contact-submissions/{contactSubmission}', [ContactSubmissionController::class, 'update'])->name('contact-submissions.update');
        Route::get('/contact-submissions/{contactSubmission}/files/{index}', [ContactSubmissionController::class, 'file'])->whereNumber('index')->name('contact-submissions.file');
    });
});
