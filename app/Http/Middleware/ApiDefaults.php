<?php

/**
 * Set User Default Information such as language, currency and timezone
 *
 * @package     HyraHotel
 * @subpackage  Middleware
 * @category    ApiDefaults
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Middleware;

use Auth;
use App;

class ApiDefaults
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, \Closure $next)
    {
        if(env('DB_DATABASE') == '' || !\Schema::hasTable('global_settings')) {
            return $next($request);
        }

        if($request->filled('token')) {
            $request->headers->set('Authorization',"Bearer ". $request->token);
        }

        $user_currency = global_settings('default_currency');
        $user_language = global_settings('default_language');
        if(Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            $user_language = $user->user_language;
            $user_currency = $user->user_currency;
            
            if($user_currency == '') {
                $geoData = getIpBasedData($_SERVER['REMOTE_ADDR']);
                $user_currency = $geoData['currency_code'];
                $user->user_currency = $user_currency;
                $user->save();
            }
        }
        if($request->locale != '') {
            $user_language = $request->locale;
        }

        if($this->checkCurrency($user_currency)) {
            $user_currency = global_settings('default_currency');
        }

        if($this->checkLanguage($user_language)) {
            $user_language = global_settings('default_language');
        }

        $currency = resolve("Currency")->where('code',$user_currency)->first();

        session(['currency' => $user_currency]);
        session(['currency_symbol' => $currency->symbol]);
        session(['language' => $user_language]);

        $min_price = ceil(currencyConvert(global_settings('min_price'),global_settings('default_currency')));
        $max_price = round(currencyConvert(global_settings('max_price'),global_settings('default_currency')));

        view()->share('min_price', $min_price);
        view()->share('max_price', $max_price);

        if(!defined('LOCALE')) {
            define('LOCALE',$user_language);
        }

        App::setLocale($user_language);

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');

        return $response;
    }

    protected function checkCurrency($currency_code)
    {
        $currency = resolve('Currency')->where('code',$currency_code)->where('status',1)->count();
        return ($currency == 0);
    }

    protected function checkLanguage($lang_code)
    {
        $language = resolve('Language')->where('code',$lang_code)->where('status',1)->where('is_translatable',1)->count();
        return ($language == 0);
    }
}