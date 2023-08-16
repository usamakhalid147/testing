<?php

/**
 * Update Configurations based on database
 *
 * @package     HyraHotel
 * @subpackage  Providers
 * @category    ConfigServiceProvider
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if(env('DB_DATABASE') != '') {
            if(Schema::hasTable('global_settings')) {
                $this->globalSettings();
            }

            if(Schema::hasTable('credentials')) {
                $this->setEmailConfig();
                $this->setCredentialsConfig();
            }

            if(Schema::hasTable('languages')) {
                $this->setTranslatableConfig();
            }

            if(global_settings('timezone') == '' && !\App::runningInConsole() && request()->segment(1) != 'install') {
                abort(500);
            }
        }

        // Custom Validation for txt File Extension
        \Validator::extend('valid_extensions', function($attribute, $value, $parameters) 
        {
            if(count($parameters) == 0) {
                return false;
            }
            $ext = strtolower($value->getClientOriginalExtension());

            return in_array($ext,$parameters);
        });
    }

    /**
     * Default Configuration from Global Settings
     *
     * @return void
     */
    protected function globalSettings()
    {
        $upload_drivers = ['0' => 'Local','1' => 'Cloudinary'];
        View::share('upload_drivers', $upload_drivers);

        $global_settings = resolve('GlobalSetting');

        $upload_drivers = View::shared('upload_drivers');
        $upload_driver = $upload_drivers[global_settings('upload_driver')] ?? 'Local';
        $image_handler = 'App\Services\ImageHandlers\\'.$upload_driver.'ImageHandler';
        $this->app->singleton('App\Contracts\ImageHandleInterface', $image_handler);

        $this->app->singleton('App\Contracts\SmsGateway', "App\Services\SmsGateway\TwilioSmsProvider");

        if(request()->has('payment_method')) {
            $payment_service = 'App\Services\Payment\\'.snakeToCamel(request()->payment_method,true).'PaymentService';
                
            $this->app->bind('App\Contracts\paymentInterface', $payment_service);            
        }

        $timezone = global_settings('timezone') != '' ? global_settings('timezone') : "UTC";
        Config::set('app.timezone',$timezone);
        date_default_timezone_set($timezone);
    }

    /**
     * Update Email Configuration
     *
     * @return void
     */
    protected function setEmailConfig()
    {
        $mail_config = $default_config = Config::get('mail');
        $mail_config['default'] = credentials('driver','EmailConfig');
        $mail_config['from'] = [
            'address' => credentials('from_address','EmailConfig'),
            'name' => credentials('from_name','EmailConfig')
        ];
        $smtp_conig = [
            'host' => credentials('host','EmailConfig'),
            'port' => credentials('port','EmailConfig'),
            'encryption' => credentials('encryption','EmailConfig'),
            'username' => credentials('username','EmailConfig'),
            'password' => credentials('password','EmailConfig'),
        ];
        $mail_config['mailers']['smtp'] = array_merge($default_config['mailers']['smtp'],$smtp_conig);

        Config::set('mail',$mail_config);
    }

    /**
     * Update Config Based On API Credentials
     *
     * @return void
     */
    protected function setCredentialsConfig()
    {
        $cloudinary_config = [
            "cloudName" => credentials('cloud_name','Cloudinary'),
            "apiKey" => credentials('api_key','Cloudinary'),
            "apiSecret" => credentials('api_secret','Cloudinary'),
            "baseUrl"  => 'http://res.cloudinary.com/'.credentials('cloud_name','Cloudinary'),
            "secureUrl"  => 'https://res.cloudinary.com/'.credentials('cloud_name','Cloudinary'),
            "apiBaseUrl"  => 'https://api.cloudinary.com/v1_1/'.credentials('cloud_name','Cloudinary'),
        ];
        $cloudinary = array_merge(Config::get('cloudinary'),$cloudinary_config);
        $default_folder = Str::of(global_settings('site_name'))->replace("."," ")->append('/')->__toString();

        Config::set('cloudinary',$cloudinary);
        Config::set('cloudinary.defaults.folder',$default_folder);
    }

    /**
     * Update Config Based On Translatable Locale
     *
     * @return void
     */
    protected function setTranslatableConfig()
    {
        $languages = resolve('Language')->where('is_translatable','1');
        $locales = $languages->pluck('code')->toArray();
        Config::set('translatable.fallback_locale',global_settings('default_language'));
    }
}
