<?php

/**
 * Register Modules
 *
 * @package     HyraHotel
 * @subpackage  Providers
 * @category    ModuleServiceProvider
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $listModule = getModuleList();
        foreach ($listModule as $module) {
            $class = "\Modules\\".ucfirst($module)."\\ModuleProvider";
            if(class_exists($class)) {
                $this->app->register($class);
            }
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $listModule = getModuleList();
        foreach ($listModule as $module) {
            if (is_dir(base_path('modules') . '/' . $module . '/Views')) {
                $this->loadViewsFrom(base_path('modules') . '/' . $module . '/Views', $module);
            }
        }
    }
}
