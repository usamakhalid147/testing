<?php

/**
 * Provide All Basic Bindings
 *
 * @package     HyraHotel
 * @subpackage  Providers
 * @category    AppServiceProvider
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Collective\Html\FormBuilder;
use Illuminate\Support\Facades\Config;
use Illuminate\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFormMacro();
        $this->registerCollectionMacro();
        if(Config::get('app.force_https')) {
            $this->app['request']->server->set('HTTPS', true);
        }

        // Load All helper files
        foreach (glob(app_path() . '/Helpers/*.php') as $file) {
            require_once($file);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        Paginator::useBootstrap();
        if(Config::get('app.force_https')) {
            $url->forceScheme('https');
        }

        // Log All database Quries
        /*\DB::listen(function($query) {
            logger($query->sql);
        });*/

        /*if(request()->segment(1) == 'api') {
            logger('requested URL : '.request()->fullUrl());
            if(request()->isMethod('POST')) {
                $post_params = request()->post();
                $post_params['token'] = request()->bearerToken();
                logger('Post Method Params : '.json_encode($post_params));
            }
        }*/

        $this->registerBladeDirectives();

        if(env('DB_DATABASE') != '') {
            if(Schema::hasTable('global_settings')) {
                $this->bindModels();
            }
            if(Schema::hasTable('reviews')) {
                \App\Models\Review::observe(\App\Observers\ReviewObserver::class);
            }
            if(Schema::hasTable('users')) {
                \App\Models\User::observe(\App\Observers\UserObserver::class);
            }

            if(Schema::hasTable('hotels')) {
                \App\Models\Hotel::observe(\App\Observers\HotelObserver::class);
            }
        }
    }

    /**
     * Register Collective Form Macro to day,month and year dropdown with attributes
     *
     * @return void
     */
    protected function registerFormMacro()
    {
        FormBuilder::macro('selectMonthWithDefault', function($name, $selected = null, $default = null, $attributes = [], $format = '%B')
        {
            $months = [];

            if ($default !== null) {
                $months['NULL'] = $default;
            }

            foreach (range(1, 12) as $month) {
                $months[$month] = strftime($format, mktime(0, 0, 0, $month, 1));
            }

            return FormBuilder::select($name, $months, isset($selected) ? $selected : $default, $attributes);
        });


        FormBuilder::macro('selectRangeWithDefault', function($name, $start, $end, $selected = null, $default = null, $attributes = [])
        {
            if ($default === null) {
                return FormBuilder::selectRange($name, $start, $end, $selected, $attributes);
            }
            if(!is_array($default)) {
                $default = array('' => $default);
            }
            $range = $default + array_combine($range = range($start, $end), $range);

            return FormBuilder::select($name, $range, $selected, $attributes);
        });
    }

    /**
     * Register Collection Macro to update Append attributes run time
     *
     * @return void
     */
    protected function registerCollectionMacro()
    {
        Collection::macro('setAppends', function ($attributes) {
            return $this->each->setAppends($attributes);
        });

        Collection::macro('activeOnly', function () {
            return $this->where('status','1')->values();
        });
    }

    /**
     * Register Collective Form Macro to day,month and year dropdown with attributes
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
        // Blade Directive to check Permission of current Admin User
        \Blade::if('checkPermission', function($permission) {
            return auth()->guard('admin')->check() && auth()->guard('admin')->user()->can($permission);
        });

        \Blade::if('checkHostPermission', function($permission) {
            return auth()->guard('host')->check() && \Auth::guard('host')->user()->can($permission);
        });

        // Dirctive to display image
        \Blade::directive('asset', function ($src) {
            return asset($src);
        });

        // Blade Directive to check Given Id is currently login user or not
        \Blade::if('checkUser', function($id) {
            return auth()->id() === $id;
        });
    }

    /**
     * Bind Commonly Used Models
     *
     * @return void
     */
    protected function bindModels()
    {
        $this->app->singleton('GlobalSetting', function() {
            return \App\Models\GlobalSetting::get();
        });

        $this->app->singleton('ReferralSetting', function() {
            return \App\Models\ReferralSetting::get();
        });
        
        $this->app->singleton('Fee', function() {
            return \App\Models\Fee::get();
        });

        $this->app->singleton('StaticPage', function() {
            return \App\Models\StaticPage::get();
        });

        $this->app->singleton('StaticPageHeader', function() {
            return \App\Models\StaticPageHeader::get();
        });

        $this->app->singleton('SocialMediaLink', function() {
            return \App\Models\SocialMediaLink::get();
        });

        $this->app->singleton('Currency', function() {
            return \App\Models\Currency::get();
        });

        $this->app->singleton('HistoricalCurrency', function() {
            return \App\Models\HistoricalCurrency::get();
        });

        $this->app->singleton('Language', function() {
            return \App\Models\Language::get();
        });

        $this->app->singleton('Country', function() {
            return \App\Models\Country::get();
        });

        $this->app->singleton('City', function() {
            return \App\Models\City::get();
        });

        $this->app->singleton('Credential', function() {
            return \App\Models\Credential::get();
        });

        $this->app->singleton('Meta', function() {
            return \App\Models\Meta::get();
        });

        $this->app->singleton('BlogCategory', function() {
            return \App\Models\BlogCategory::get();
        });

        $this->app->singleton('Blog', function() {
            return \App\Models\Blog::get();
        });

        $this->app->singleton('HelpCategory', function() {
            return \App\Models\HelpCategory::get();
        });

        $this->app->singleton('Help', function() {
            return \App\Models\Help::get();
        });

        $this->app->singleton('RoomType', function() {
            return \App\Models\RoomType::get();
        });

        $this->app->singleton('PropertyType', function() {
            return \App\Models\PropertyType::get();
        });

        $this->app->singleton('BedType', function() {
            return \App\Models\BedType::get();
        });

        $this->app->singleton('AmenityType', function() {
            return \App\Models\AmenityType::get();
        });

        $this->app->singleton('Amenity', function() {
            return \App\Models\Amenity::get();
        });

        $this->app->singleton('GuestAccess', function() {
            return \App\Models\GuestAccess::get();
        });

        $this->app->singleton('HotelRule', function() {
            return \App\Models\HotelRule::get();
        });

        $this->app->singleton('PopularCity', function() {
            return \App\Models\PopularCity::get();
        });

        $this->app->singleton('MealPlan', function() {
            return \App\Models\MealPlan::get();
        });

        $this->app->singleton('HotelRoomPriceRule', function() {
            return \App\Models\HotelRoomPriceRule::get();
        });

        $this->app->singleton('DateFormat', function() {
            $path = app_path('Models/DateFormats.json');
            $date_formats = collect(json_decode(file_get_contents($path), true));
            return $date_formats;
        });

        $this->app->singleton('MessageType', function() {
            $message_types = [
                ['id' => '1','name' => 'booking_discuss'],
                ['id' => '2','name' => 'booking_accepted'],
                ['id' => '3','name' => 'booking_requested'],
                ['id' => '4','name' => 'booking_pre_accepted'],
                ['id' => '5','name' => 'booking_declined'],
                ['id' => '6','name' => 'booking_expired'],
                ['id' => '7','name' => 'guest_cancel_request'],
                ['id' => '8','name' => 'guest_cancel_booking'],
                ['id' => '9','name' => 'host_cancel_booking'],
                ['id' => '10','name'=> 'resubmit_room'],
                ['id' => '11','name'=> 'resubmit_id'],
                ['id' => '12','name'=>  'contact_request_sent'],
                ['id' => '13','name'=>  'request_pre_approved'],
                ['id' => '14','name'=>  'special_offer'],
            ];
            return collect($message_types);
        });
    }
}