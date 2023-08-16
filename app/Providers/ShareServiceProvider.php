<?php

/**
 * Provide All Basic Data Needed for all over site
 *
 * @package     HyraHotel
 * @subpackage  Providers
 * @category    ShareServiceProvider
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Lang;

class ShareServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $status = ['0' => Lang::get('messages.inactive'), '1' => Lang::get('messages.active')];
        View::share('status_array', $status);

        $yes_no = ['0' => Lang::get('messages.no'), '1' => Lang::get('messages.yes')];
        View::share('yes_no_array', $yes_no);

        $valid_mimes = 'jpeg,jpg,png,webp';
        View::share('valid_mimes', $valid_mimes);

        $valid_video_mimes = 'mp4,webm';
        View::share('valid_video_mimes', $valid_video_mimes);

        $cancellation_policies  = [
            'flexible' => Lang::get('messages.flexible'),
            'moderate' => Lang::get('messages.moderate'),
            'strict' => Lang::get('messages.strict'),
        ];
        View::share('cancellation_policies', $cancellation_policies);

        $booking_types  = [
            'request_book' => Lang::get('messages.listing.request_book'),
            'instant_book' => Lang::get('messages.listing.instant_book'),
        ];
        View::share('booking_types', $booking_types);

        $guests_array = array();
        for($i=1;$i<=16;$i++) {
            $value = ($i == 16) ? $i.'+' : $i;
            $guests_array[] = [
                'key' => $i,
                "value" => $value,
                "display_text" => $value.' '.Lang::choice("messages.listing.guest",$i),
            ];
        }
        
        view()->share('guests_array',$guests_array);
        
        View::share('max_guests', 16);

        $price_range_array = array();
        for($i=1;$i<=5;$i++) {
            $value = '';
            for($j=1;$j<=$i;$j++) {
                $value .= global_settings('price_range_symbol');
            }
            $price_range_array[] = [
                'key' => $i,
                "value" => $value,
            ];
        }
        
        view()->share('price_range_array',$price_range_array);

        $star_rating_array = array();
        for($i=1;$i<=5;$i++) {
            $value = '';
            for($j=1;$j<=$i;$j++) {
                $value .= '*';
            }
            $star_rating_array[] = [
                'key' => $i,
                "value" => $value,
            ];
        }
        view()->share('star_rating_array',$star_rating_array);


        view()->share('default_checkin',\Carbon\Carbon::today()->format('Y-m-d'));
        view()->share('default_checkout',\Carbon\Carbon::tomorrow()->format('Y-m-d'));


        if(env('DB_DATABASE') != '') {
            
            if(Schema::hasTable('global_settings')) {
                $this->siteSettings();
                $this->shareDateFormat();
            }

            if(Schema::hasTable('static_pages') && Schema::hasTable('static_page_headers')) {
                $this->pages();
            }
            if(Schema::hasTable('countries')) {
                $this->country();
            }
            if(Schema::hasTable('cities')) {
                $this->city();
            }
            if(Schema::hasTable('currencies')) {
                $this->currency();
            }
            if(Schema::hasTable('languages')) {
                $this->languages();
            }
            if(Schema::hasTable('static_page_headers')) {
                $this->staticPageHeader();
            }
        }
    }

    /**
     * Share Global Settings data to whole app
     *
     * @return void
     */
    protected function siteSettings()
    {
        $global_settings = resolve('GlobalSetting');
        if($global_settings->count()) {
            if($global_settings[1]->value == '' && @$_SERVER['HTTP_HOST'] && !\App::runningInConsole()) {
                $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                $url  = $protocol.$_SERVER['HTTP_HOST'];
                $url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
                \App\Models\GlobalSetting::where('name','site_url')->update(['value' =>  $url]);
            }

            View::share('site_name', global_settings('site_name'));
            View::share('site_url', siteUrl());
            View::share('version', global_settings('version'));
            View::share('version', \Str::random(4)); // Load All JS & CSS without Cache
            
            if(!defined('SITE_NAME')) {
                define('SITE_NAME', global_settings('site_name'));
            }

            if(!defined("SIMILAR_ROOM_DISTANCE")) {
                define("SIMILAR_ROOM_DISTANCE",30);
            }

            if(!defined("WEEKEND_DAYS")) {
                define("WEEKEND_DAYS",[5,6]);
            }

            if(!defined("VERIFICATION_METHODS")) {
                $verification_methods = array(
                    'email',
                    'google',
                    'facebook',
                    'phone_number',
                );
                define("VERIFICATION_METHODS",$verification_methods);
            }

            if(!defined("MAX_REVIEW_DAYS")) {
                define("MAX_REVIEW_DAYS",global_settings('max_review_days'));
            }

            if(!defined("MAX_GUEST_DISPUTE_DAYS")) {
                define("MAX_GUEST_DISPUTE_DAYS",global_settings('max_guest_dispute_days'));
            }

            if(!defined("MAX_HOST_DISPUTE_DAYS")) {
                define("MAX_HOST_DISPUTE_DAYS",global_settings('max_host_dispute_days'));
            }

            if(!defined("DELETE_STORAGE")) {
                $delete_storage = env("DELETE_STORAGE",true) == true;
                define("DELETE_STORAGE",$delete_storage);
            }

            if(!defined("MAIL_DRIVERS")) {
                define("MAIL_DRIVERS",["smtp" => "SMTP", "sendmail" => "Send Mail (Local)"]);
            }

            if(!defined("PAYOUT_METHODS")) {
                $payout_methods = array(
                    // ["key" => 'paypal', "value" => "Paypal","display_name" => 'paypal'],
                    ["key" => 'stripe', "value" => "Stripe","display_name" => 'stripe'],
                    ["key" => 'bank_transfer', "value" => "BankTransfer","display_name" => 'bank_transfer'],
                );
                define("PAYOUT_METHODS",$payout_methods);
            }

            if(!defined("PAYMENT_METHODS")) {
                $payment_methods = array(
                    ["key" => 'one_pay', "value" => "OnePay", "display_name" => 'credit_or_debit_card'],
                    // ["key" => 'paypal', "value" => "Paypal", "display_name" => 'paypal'],
                    ["key" => 'stripe', "value" => "Stripe", "display_name" => 'credit_or_debit_card'],
                    ["key" => 'pay_at_hotel', "value" => "PayHotel", "display_name" => 'pay_at_hotel'],
                );
                define("PAYMENT_METHODS",$payment_methods);
            }

            if(!defined("NO_HEADER_ROUTES")) {
                $no_header_routes = array(
                    'add_payout',
                    'payment.home',
                    'manage_hotel',
                    'manage_experience',
                    'experience_payment.home',
                    'create_listing',
                );
                define("NO_HEADER_ROUTES",$no_header_routes);
            }

            if(!defined("NO_FOOTER_ROUTES")) {
                $no_header_routes = array(
                    'add_payout',
                    // 'payment.home',
                    'create_hotel',
                    'manage_hotel',
                    // 'contact_us',
                    'manage_experience',
                    'experience_payment.home',
                );
                define("NO_FOOTER_ROUTES",$no_header_routes);
            }

            if(!defined("HOST_CANCEL_REASONS")) {
                $host_cancel_reasons = array(
                    'no_longer_available',
                    'offer_a_different_hotel',
                    'need_maintenance',
                    'my_guest_needs_to_cancel',
                    'other',
                );
                define("HOST_CANCEL_REASONS",$host_cancel_reasons);
            }

            if(!defined("GUEST_CANCEL_REASONS")) {
                $guest_cancel_reasons = array(
                    'no_longer_need_this_hotel',
                    'travel_dates_changed',
                    'made_the_reservation_by_accident',
                    'my_host_needs_to_cancel',
                    'uncomfortable_with_the_host',
                    'place_not_okay',
                    'other',
                );
                define("GUEST_CANCEL_REASONS",$guest_cancel_reasons);
            }

            if(!defined("HOST_DECLINE_REASONS")) {
                $host_decline_reasons = array(
                    'dates_are_not_available',
                    'not_comfortable',
                    'offer_a_different_hotel',
                    'waiting_for_better_reservation',
                    'this_request_is_spam',
                    'other',
                );
                define("HOST_DECLINE_REASONS",$host_decline_reasons);
            }

            $site_logo = $this->getImageUrl('logo');
            $favicon = $this->getImageUrl('favicon');

            View::share('site_logo', $site_logo);
            View::share('favicon', $favicon);
            
            View::share('default_currency', global_settings('default_currency'));
        }
    }

    /**
     * Share Static Page data to whole app
     *
     * @return void
     */
    protected function pages()
    {
        $pages = resolve("StaticPage")->where('status',1)->where('in_footer',1);
        $footer_sections = array();
        $static_page_headers = resolve("StaticPageHeader")->pluck('display_name','id');
        foreach ($static_page_headers as $key => $section) {
            $footer_data = $pages->where('under_section',$key);
            if($footer_data->count() || $key == 'about') {
                $footer_sections[$section] = $footer_data;
            }
        }
        View::share('footer_sections', $footer_sections);

        $agree_pages = resolve("StaticPage")->where(
            'status',1)->where('must_agree',1);
        View::share('agree_pages', $agree_pages);
    }

    /**
     * Share country data to whole app
     *
     * @return void
     */
    protected function country()
    {
        $country = resolve('Country');
        $country_list = $country->pluck('full_name','name');
        $default_country_code = optional(resolve("Country")->first())->name ?? 'IN';
        View::share('default_country_code', $default_country_code);
        View::share('country_list', $country_list);

        $countries = $country->where('city_count','>',0)->values();
        View::share('countries',$countries);
    }

    /**
     * Share city data to whole app
     *
     * @return void
     */
    protected function city()
    {
        $city = resolve('City');
        $city_list = $city->activeOnly();
        View::share('city_list', $city_list);
    }

    /**
     * Share Currency data to whole app
     *
     * @return void
     */
    protected function currency()
    {
        $currency = resolve('Currency')->where('status','1');
        $currency_list = $currency->pluck('code','code');
        View::share('currency_list', $currency_list);
    }

    /**
     * Share Language data to whole app
     *
     * @return void
     */
    protected function languages()
    {
        $languages = resolve('Language');
        $language_list = $languages->where('status','1')->pluck('name','code');
        View::share('language_list', $language_list);
        $language_list = $languages->where('status','1')->where('is_translatable','1')->pluck('name','code');
        View::share('translatable_languages', $language_list);
    }

    /**
     * Share static Page Header data to whole app
     *
     * @return void
     */
    protected function staticPageHeader()
    {
        $static_page_headers = resolve('StaticPageHeader')->pluck('display_name','id');
        View::share('static_page_headers', $static_page_headers);
    }

    /**
     * Set Application Date Format 
     *
     * @return void
     */
    protected function shareDateFormat()
    {
        $date_formats = resolve("DateFormat");
        $selected_format = $date_formats->where('id',global_settings('date_format') ?? '1')->first();
        view()->share('selected_format',$selected_format);
        
        view()->share('date_format', $selected_format['php_format']);
        if(!defined('DATE_FORMAT')) {
            define('DATE_FORMAT', $selected_format['php_format']);
        }

        view()->share('time_format','h:i A');
        if(!defined('TIME_FORMAT')) {
            define('TIME_FORMAT', 'h:i A');
        }
    }

    /**
     * Get Image Url
     *
     * @return String Image Url
     */
    protected function getImageUrl($type)
    {
        $global_settings = resolve("GlobalSetting");
        $upload_drivers = view()->shared('upload_drivers');

        $global_setting = $global_settings->where('name',$type)->first();
        $upload_driver = $global_settings->where('name',$type.'_driver')->first();
        $handler = resolve('App\Services\ImageHandlers\\'.$upload_drivers[$upload_driver->value].'ImageHandler');
        $image_data['name'] = $global_setting->value;
        $image_data['version_based'] = true;
        $image_data['path'] = $global_setting->filePath;

        return $handler->fetch($image_data);
    }
}
