<?php

/**
 * Provide All the Routes for the App
 *
 * @package     HyraHotel
 * @subpackage  Providers
 * @category    RouteServiceProvider
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

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
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->prefix($this->getAdminPrefix())
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->prefix($this->getHostPrefix())
                ->name('host.')
                ->group(base_path('routes/host.php'));

            if(global_settings('is_locale_based')) {
                Route::middleware('web')
                ->prefix('{locale?}')
                ->group(base_path('routes/web.php'));
            }
            else {
                Route::middleware('web')
                ->group(base_path('routes/web.php'));
            }
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Get Admin prefix
     *
     * @return string
     */
    protected function getAdminPrefix()
    {
        $admin_prefix = "admin";

        if (env('DB_DATABASE') != '' && \Schema::hasTable('global_settings')) {
            $admin_prefix = global_settings('admin_url');
        }
        
        return $admin_prefix;
    }

    /**
     * Get Host prefix
     *
     * @return string
     */
    protected function getHostPrefix()
    {
        $host_prefix = "host";

		if (env('DB_DATABASE') != '' && \Schema::hasTable('global_settings')) {
            $host_prefix = global_settings('host_url');
        }
        
        return $host_prefix;
    }
}
