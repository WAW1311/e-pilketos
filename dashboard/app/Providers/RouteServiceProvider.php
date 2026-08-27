<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(strtolower((string) $request->input('email')) . '|' . $request->ip());
        });

        // Limiter umum untuk seluruh grup 'api'. Semua request mobile memakai
        // token kiosk yang sama, jadi dikunci per-IP (bukan per-user).
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

    //     // Ambil data surat suara.
    //     RateLimiter::for('votepapper', function (Request $request) {
    //         return Limit::perMinute(60)->by($request->ip());
    //     });

    //     // Verifikasi NIS — diperketat per-IP untuk memperlambat enumerasi NIS.
    //     RateLimiter::for('verify', function (Request $request) {
    //         return Limit::perMinute(40)->by($request->ip());
    //     });

    //     // Submit suara — dikunci per-IP.
    //     RateLimiter::for('voting', function (Request $request) {
    //         return Limit::perMinute(40)->by($request->ip());
    //     });
    }
}
