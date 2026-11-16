<?php

namespace App\Providers;

use App\Models\Candidate;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('contact-form', function (Request $request) {
            return Limit::perMinute(6)->by($request->ip());
        });

        // відгук із резюме: усіх запитів з однієї адреси — до 20 на годину
        // (успішних — до 5, це рахує сам CareerApplicationController)
        RateLimiter::for('career-apply', function (Request $request) {
            return Limit::perHour(20)->by($request->ip())->response(fn () => response()->json(
                ['message' => 'Забагато спроб. Спробуйте пізніше.'],
                429,
            ));
        });

        // лічильник нових відгуків у меню
        View::composer('admin.layout', function ($view) {
            $view->with('hrNewCandidates', auth()->check() ? Candidate::where('status', 'new')->count() : 0);
        });
    }
}
