<?php

/**
 * Set User Default Information such as language, currency and timezone
 *
 * @package     HyraHotel
 * @subpackage  Middleware
 * @category    UserDefaults
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Middleware;

use Auth;
use App;

class UserDefaults
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

        if($request->bearerToken() != '' && Auth::guard('api')->check()) {
            session(['is_mobile' => true]);
        }

        if(Auth::check() && !isAdmin()) {
            $user = Auth::user();
            $user_language = $user->user_language;
            $user_currency = $user->user_currency;
            
            if($user_currency == '') {
                $geoData = getIpBasedData($_SERVER['REMOTE_ADDR']);
                $user_currency = $geoData['currency_code'];
                $user->user_currency = $user_currency;
                $user->save();
            }
        }
        else if(isAdmin()) {
            $user_currency = global_settings('default_currency');
            $user_language = global_settings('default_language');
            if(Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $user_language = $user->user_language;
                $user_currency = $user->user_currency;
            }
        }
        else {
            if(session('currency') != '') {
                $user_currency = session("currency");
            }
            else {
                $geoData = getIpBasedData($_SERVER['REMOTE_ADDR']);
                $user_currency = $geoData['currency_code'];
            }

            if(session('language') != '') {
                $user_language = session("language");
            }
            else {
                $user_language = global_settings('default_language');
            }
        }

        if(isHost()) {
            $reviews_count = \App\Models\Review::where('user_to',getHostId())->where('public_reply','')->count();
            view()->share('reviews_count', $reviews_count);

            // $messages_count = \App\Models\Message::where('host_id',Auth::id())->where('host_read',0)->count();
            // view()->share('messages_count', $messages_count);
        }

        if($this->checkCurrency($user_currency)) {
            $user_currency = global_settings('default_currency');
        }

        if($this->checkLanguage($user_language)) {
            $user_language = global_settings('default_language');
        }

        if(global_settings('is_locale_based') && !isApiRequest()) {
            $locale = $request->locale;
            if(!$locale || \Str::Length($locale) != 2 || $locale != $user_language) {
                $locale = $user_language;
                $redirect = true;
            }

            if($this->checkLanguage($locale)) {
                $locale = $user_language;
                $redirect = true;
            }
            if(isset($redirect) && $request->segment(1) != global_settings('admin_url')) {
                $url = $this->formatURL($request,$locale);
                return redirect()->to($url);
            }            
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

        if(Auth::check()) {
            if (Auth::user()->user_type == 'sub_host') {
                if(\Route::currentRouteName() == 'dashboard') {
                    $redirect_url = resolveRoute('host.dashboard');
                    return redirect($redirect_url);                
                }
                $route = explode('.',\Route::currentRouteName());
                if (!in_array($route[0],['host','logout'])) {
                    abort(403);
                }
            }
        }

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

    public function formatURL($request,$locale)
    {
        if ($request->method() === 'GET') {
            $segments = $request->segments();
            if(\Str::Length($locale) != 2 || (isset($segments[0]) && \Str::Length($segments[0]) != 2 )) {
                $segments = \Arr::prepend($segments, $locale);
            }
            else {
                $segments[0] = $locale;
            }
            $query_string = $request->getQueryString();
            $parsed_url = implode('/', $segments);
            if($query_string != '') {
                $parsed_url .= '?'.$query_string;
            }
            return url($parsed_url);
        }
        return $request->url();
    }
}