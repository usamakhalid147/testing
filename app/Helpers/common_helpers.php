<?php

/**
 * Common Helper functions
 *
 * @package     HyraHotel
 * @subpackage  Helpers
 * @category    Common Helper functions
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

/**
 * Resolve Global Settings and get value of given string
 *
 * @param  string $key Name of the value to get
 * @return String
 */
if (!function_exists('global_settings')) {

    function global_settings($key)
    {
        try {
            $global_settings = resolve('GlobalSetting');
            $global_setting = $global_settings->where('name',$key)->first();
            
            return optional($global_setting)->value;
        }
        catch (\Exception $e) {
            return '';
        }
    }
}

/**
 * Resolve Global Settings and get value of given string
 *
 * @param  string $key Name of the value to get
 * @return String
 */
if (!function_exists('referral_settings')) {

    function referral_settings($key)
    {
        try {
            $referral_settings = resolve('ReferralSetting');
            $referral_setting = $referral_settings->where('name',$key)->first();
            $value = optional($referral_setting)->value;
            if($referral_setting->name == 'is_enabled') {
                return $value;
            }
            return currencyConvert($value, global_settings('default_currency'));
        }
        catch (\Exception $e) {
            return '';
        }
    }
}

/**
 * Resolve Credentials and get value of given string
 *
 * @param  string $key Name of the value to get
 * @param  string $site Name of the site to get
 * @return String
 */
if (!function_exists('credentials')) {

    function credentials($key, $site)
    {
        $credentials = resolve('Credential');
        $credential = $credentials->where('name',$key)->where('site',$site)->first();
        
        return optional($credential)->value;
    }
}

/**
 * Resolve Fees and get value of given string
 *
 * @param  string $key Name of the value to get
 * @return String
 */
if (!function_exists('fees')) {

    function fees($key)
    {
        $fees = resolve('Fee');
        $fee = $fees->where('name',$key)->first();
        
        return optional($fee)->value;
    }
}

/**
 * Resolve Meta and get value of current Route
 *
 * @param  string $field Name of the value to get
 * @return String
 */
if (!function_exists('getMetaData')) {

    function getMetaData($field,$default_value = '')
    {
        $meta_data = resolve('Meta');
        $page_data = $meta_data->where('route_name',Route::currentRouteName())->first();
        if($field == 'title') {
            $title = optional($page_data)->title;
            if($title == '') {
                return Lang::get('messages.page_not_found');
            }
            $replace_keys = ['{SITE_NAME}','{SLUG}','{LIST_NAME}','{PAGE}'];
            if(request()->page != '') {
                $page = Str::of(request()->page)->replace('-',' ')->title();
            }
            $replace_values = [SITE_NAME,request()->slug ?? '',view()->shared('wishlist_name') ?? '',$page ?? ''];
            return Str::of($title)->replace($replace_keys,$replace_values);
        }
        return optional($page_data)->$field ?? $default_value;
    }
}

/**
 * Resolve Social Media and get value of given string
 *
 * @param  string $key Name of the value to get
 * @return String
 */
if (!function_exists('social_media')) {

    function social_media($key)
    {
        $social_media = resolve('SocialMediaLink');
        $config = $social_media->where('name',$key)->first();
        
        return optional($config)->value;
    }
}

/**
 * Check Given Feature Enabled or Not
 *
 * @param  string $site Name of the site to get
 * @return Boolean
 */
if (!function_exists('checkEnabled')) {

    function checkEnabled($site)
    {
        $is_enabled = credentials('is_enabled',$site);
        
        return $is_enabled == '1';
    }
}

/**
 * Check Can display default Credentials
 *
 * @return Boolean
 */
if (!function_exists('displayCrendentials')) {
    function displayCrendentials()
    {
        return (env('SHOW_CREDENTIALS','false') == 'true');
    }
}

/**
 * Check Can display Test or Dummy Data
 *
 * @return Boolean
 */
if (!function_exists('displayTestData')) {
    function displayTestData()
    {
        return (env('APP_ENV','local') == 'live');
    }
}

/**
 * Check Can display default Credentials
 *
 * @return Boolean
 */
if (!function_exists('isSecure')) {
    function isSecure()
    {
        return request()->isSecure();
    }
}

/**
 * Get Status Text of give code
 *
 * @param  Int $status
 * @param  string $status_text text related to given status code
 */
if(!function_exists('getStatusText')) {
    function getStatusText($status)
    {
         $array = [
            '1' => Lang::get('messages.active'),
            '0' => Lang::get('messages.inactive'),
        ];
        return $array[$status] ?? '';
    }
}

/**
 * Get Yes Or No Text of give code
 *
 * @param  Int $value
 * @param  string $yes_no_text related to given value
 */
if(!function_exists('getYesNoText')) {
    function getYesNoText($value)
    {
        $array = [
            '1' => Lang::get('messages.yes'),
            '0' => Lang::get('messages.no'),
        ];
        return $array[$value] ?? '';
    }
}

/**
 * Set Flash Message function
 *
 * @param  string $state     Type of the state ['danger','success','warning','info']
 * @param  string $message   message to be displayed
 */
if(!function_exists('flashMessage')) {
    function flashMessage($state, $title, $message)
    {
        Session::flash('state', $state);
        Session::flash('title', $title);
        Session::flash('message', $message);
    }
}

/**
 * File Get Content by using CURL alternative to file_get_contents
 *
 * @param string $url
 * @return mixed
 */
if (!function_exists('file_get_contents_curl')) {

    function file_get_contents_curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}

/**
 * Check Given IP Address is valid or not
 *
 * @param string $ip_addr
 * @return boolan
 */
if (!function_exists('isValidIpAddr')) {
    function isValidIpAddr($ip_addr)
    {
        return preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $ip_addr);
    }
}

/**
 * Get IP Based Data
 *
 * @param string $ip_addr
 * @return Array Data
 */
if (!function_exists('getIpBasedData')) {
    function getIpBasedData($ip_addr)
    {
        $currency_code = global_settings('default_currency');
        $language = global_settings('default_language');
        $timezone = global_settings('timezone');
        $country_code = resolve("Country")->first()->name;
        $address = '';
        if(isValidIpAddr($ip_addr)) {
            $url = "http://www.geoplugin.net/php.gp?ip=".$ip_addr;
            $result = file_get_contents_curl($url);
            $geoResult = @unserialize($result);
            if(isset($geoResult["geoplugin_currencyCode"])) {
                // $currency_code = $geoResult["geoplugin_currencyCode"];
            }
            if(isset($geoResult["geoplugin_countryCode"])) {
                $country_code = $geoResult["geoplugin_countryCode"];
            }
            if(isset($geoResult["geoplugin_timezone"])) {
                $timezone = $geoResult["geoplugin_timezone"];
            }
            if(isset($geoResult["geoplugin_city"])) {
                $address .= $geoResult["geoplugin_city"].', ';
            }
            if(isset($geoResult["geoplugin_region"])) {
                $address .= $geoResult["geoplugin_region"].', ';
            }
            if(isset($geoResult["geoplugin_countryName"])) {
                $address .= $geoResult["geoplugin_countryName"].', ';
            }
            $address = rtrim($address,', ');
        }

        $country = resolve("Country")->where('name',$country_code)->count();
        if($country == 0) {
            $country_code = resolve("Country")->first()->name;
        }

        $currency = resolve("Currency")->where('code',$currency_code)->count();
        if($currency == 0) {
            $currency_code = global_settings('default_currency');
        }

        return compact("currency_code","country_code","language","timezone",'address');
    }
}

/**
 * Resolve Route
 *
 * @return Url
 */
if (!function_exists('resolveRoute')) {
    function resolveRoute($routeName,$params = [])
    {
        if(global_settings('is_locale_based') && defined('LOCALE') && !isset($params['locale'])) {
            $params['locale'] = LOCALE;
        }
        return route($routeName,$params);
    }
}

/**
 * Reduce String with given length
 *
 * @param String $string
 * @param Integer $length
 *
 * @return String $string
 */
if (!function_exists('truncateString')) {
    function truncateString($string,$length = 90)
    {
        if (strlen($string) > $length) {
            $string = substr($string, 0, $length+1);
            $pos = strrpos($string, ' ');
            $string = substr($string, 0, ($pos > 0)? $pos : $length).'...';
        }
        return $string;
    }
}

/**
 * Convert Br to New line
 *
 * @param String $string
 *
 * @return String $string
 */
if (!function_exists('br2nl')) {
    function br2nl($string)
    {
        return preg_replace('/<br\s?\/?>/ius', "\n", str_replace("\n","",str_replace("\r","", htmlspecialchars_decode($string))));
    }
}

/**
 * Get Site Base Url
 *
 * @return String $url Base url
 */
if (!function_exists('siteUrl')) {

    function siteUrl()
    {
        $global_settings_url = global_settings('site_url');
        $url = \App::runningInConsole() ? $global_settings_url : url('/');
        return $url;
    }
}

/**
 * get Module List
 *
 * @return array $modules
 */
if (!function_exists('getModuleList')) {

    function getModuleList()
    {
        $modules = array_map('basename', array_filter(glob(base_path().'/modules/*'), 'is_dir'));
        return $modules;
    }
}

/**
 * Check Given Module is Enabled or Not
 *
 * @param  string $module Name
 * @return Boolean
 */
if (!function_exists('isModuleEnabled')) {

    function isModuleEnabled($module)
    {
        $modules = getModuleList();
        return in_array($module,$modules);
    }
}

/**
 * Check Current Route is inside given array
 *
 * @param  String route names
 * @return boolean true|false
 */
if (!function_exists('isActiveRoute')) {

    function isActiveRoute()
    {
        $routes = func_get_args();
        return in_array(Route::currentRouteName(),$routes);
    }
}

/**
 * Check Given Request is from API or not
 *
 * @return Boolean
 */
if (!function_exists('isApiRequest')) {

    function isApiRequest()
    {
        return request()->segment(1) == 'api';
    }
}

/**
 * Check is admin panel or not
 *
 * @param string $hostUserId
 * @return Boolean
 */
if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return Request::segment(1) == global_settings('admin_url');
    }
}


/**
 * Check is host panel or not
 *
 * @param string $hostUserId
 * @return Boolean
 */
if (!function_exists('isHost')) {
    function isHost()
    {
        return Request::segment(1) == global_settings('host_url');
    }
}


/**
 * Check Given Currency is Valid or not
 *
 * @param  String currency_code
 * @return boolean true|false
 */
if (!function_exists('isActiveCurrency')) {

    function isActiveCurrency($currency_code)
    {
        $currency = resolve('Currency')->where('code',$currency_code)->where('status','1')->count();
        return ($currency > 0);
    }
}

/**
 * Calculate Age and check age is above 18
 *
 * @param  date $date
 * @param  String $format
 * @return  string $boolean true or false
 */
if(!function_exists('isAbove18')) {
    function isAbove18($date,$format = 'Y-m-d')
    {
        $date_obj = Carbon::createFromFormat($format, $date);
        return $date_obj && $date_obj->age >= 18;
    }
}

/**
 * Check Given Date is Valid or Not
 *
 * @param  date $date
 * @param  String $format
 * @return  string $boolean true or false
 */
if(!function_exists('isValidDate')) {
    function isValidDate($date,$format = 'Y-m-d')
    {
        $date_obj = Carbon::createFromFormat($format, $date);
        return $date_obj && $date_obj->format($format) == $date;
    }
}

/**
 * Check Given Date is past or Not
 *
 * @param  date $date
 * @param  String $format
 * @return  string $boolean true or false
 */
if(!function_exists('checkInValidDate')) {
    function checkInValidDate($start,$end,$format = 'Y-m-d')
    {
        try {
            $start = Carbon::createFromFormat($format, $start);
            $end = Carbon::createFromFormat($format, $end);
            if (strtotime($start) < strtotime(date('Y-m-d')) || strtotime($end) - strtotime($start) < 86400 || $end->isPast()) {
                $start = Carbon::today();
                $end = Carbon::tomorrow();
            }
        }
        catch(\Exception $e) {
            $start = Carbon::today();
            $end = Carbon::tomorrow();
        }
        return [$start,$end];
    }
}

/**
 * Check given input is timestamp or not
 *
 * @param String|Timestamp $timestamp
 * @return Boolean
 */
if (!function_exists('isValidTimeStamp')) {
    function isValidTimeStamp($timestamp)
    {
        try {
            new DateTime('@'.$timestamp);
        }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}

/**
 * Get Carbon Date Object from Given date or timestamp
 *
 * @param String|Timestamp $date
 * @return Object $date_obj  instance of Carbon\Carbon
 */
if (!function_exists('getDateObject')) {
    function getDateObject($date = '')
    {
        if($date == '') {
            $date_obj = Carbon::now();
        }
        else if(isValidTimeStamp($date)) {
            $date_obj = Carbon::createFromTimestamp($date);
        }
        else {
            $date_obj = Carbon::createFromTimestamp(strtotime($date));
        }
        return $date_obj;
    }
}

/**
 * Get days between two dates
 *
 * @param date $startDate  Start Date
 * @param date $endDate    End Date
 * @return array $dates    Between two dates
 */
if (!function_exists('getDays')) {
    function getDays($startDate, $endDate)
    {
        $start = getDateObject($startDate);
        $end = getDateObject($endDate);

        $dates = [];
        while($start->lte($end)) {
            $dates[] = $start->copy()->format('Y-m-d');
            $start->addDay();
        }
        return $dates;
    }
}

/** 
 * create time range 
 *  
 * @param mixed $start start time 
 * @param mixed $end   end time
 * @param string $interval time intervals, 1 hour, 1 mins, 1 secs, etc.
 * @param string $format time format, e.g., 12 or 24
 */
if (!function_exists('generateTimeRange')) {
    function generateTimeRange($start, $end, $interval = '30 mins', $format = '12')
    {
        $startTime = strtotime($start); 
        $endTime   = strtotime($end);
        $returnTimeFormat = ($format == '12')?'h:i A':'H:i A';

        $current   = time();
        $addTime   = strtotime('+'.$interval, $current); 
        $diff      = $addTime - $current;

        $times = array();
        while ($startTime < $endTime) {
            $times[date('H:i:s',$startTime)] = date($returnTimeFormat, $startTime); 
            $startTime += $diff; 
        }
        if($end == "24:00") {
            $times[date('H:i:s',$startTime-60)] = date($returnTimeFormat, $startTime-60); 
        }
        return $times;
    }
}

/**
 * number Format function 
 *
 * @param  Mixed $value
 * @return String $value
 */
if(!function_exists('numberFormat')) {
    function numberFormat($value,$range = 2)
    {
        if($value > 0) {
            return number_format(floatval($value),$range,'.','');
        }
        return '0';
    }
}

/**
 * number Format function 
 *
 * @param  Mixed $value
 * @return String $value
 */
if(!function_exists('numberFormatDisplay')) {
    function numberFormatDisplay($value)
    {
        if($value > 0) {
            return number_format($value,0,'.','');
        }
        return '0';
    }
}

/**
 * Get Number of Hours Between two hours
 *
 * @param  time $start_time
 * @param  time $end_time
 * @param  String $format Time Format, default H:i:s
 * @return Int $diff_hours
 */
if(!function_exists('getTotalHours')) {
    function getTotalHours($start_time, $end_time, $format = 'H:i:s')
    {
        $start_time = Carbon::createFromFormat($format, $start_time);
        $end_time   = Carbon::createFromFormat($format, $end_time);

        if($end_time->format('H:i') == "23:59") {
            $end_time->addMinute();
        }
        return $start_time->diffInHours($end_time);
    }
}

/**
 * Convert underscore_strings to camelCase (medial capitals).
 *
 * @param {string} $str
 *
 * @return {string}
 */
if (!function_exists('snakeToCamel')) {
    
    function snakeToCamel($str,$removeSpace = false) {
        // Remove underscores, capitalize words, squash.
        $camelCaseStr =  ucwords(str_replace('_', ' ', $str));
        if($removeSpace) {
            $camelCaseStr =  str_replace(' ', '', $camelCaseStr);
        }
        return $camelCaseStr;
    }
}

/**
 * Calculate Percentage Amount
 *
 * @param Float $price
 * @return Float $percentage_amount
 */
if(!function_exists('calculatePercentageAmount')) {
    function calculatePercentageAmount($percentage,$price)
    {
        $percentage_amount = ($percentage / 100) * $price;
        
        return numberFormat($percentage_amount);
    }
}

/**
 * Convert Amount for target Currency
 *
 * @param String $source Source Currency to be converted
 * @param String $target Target Currency to be converted
 * @param Float $price  Price Amount
 * @return Float Converted amount
 */
if(!function_exists('currencyConvert')) {
    function currencyConvert($price, $source, $target = '')
    {
        if($target == '') {
            $target = session('currency');
        }
        if($price == 0) {
            return numberFormat($price);
        }

        if($source == $target) {
            return numberFormat($price);
        }

        $rate = resolve('Currency')->where('code',$source)->first()->rate;
        $target_currency = resolve('Currency')->where('code',$target)->first();

        $session_rate = '1';
        if($target_currency) {
            $session_rate = $target_currency->rate;
        }

        if($rate == "0.0") {
            dd("Error Message : Currency value '0' (". $source . ')');
        }
        $converted_price = $price / $rate;

        return numberFormat($converted_price * $session_rate);
    }
}

/**
 * Send Notification to User
 *
 * @return Array
 */
if (!function_exists('resolveAndSendNotification')) {
    function resolveAndSendNotification($functionName,...$args)
    {
        $notification_service = resolve("App\Services\NotificationService");
        $return_data = $notification_service->$functionName(...$args);
        return $return_data;
    }
}

/**
 * Send Notification to User
 *
 * @return Array
 */
if (!function_exists('resolveAndSendExperienceNotification')) {
    function resolveAndSendExperienceNotification($functionName,...$args)
    {
        $notification_service = resolve("Modules\Experience\Services\NotificationService");
        $return_data = $notification_service->$functionName(...$args);
        return $return_data;
    }
}

/**
 * Traverse Collection to Get All Relation to single Collection
 *
 * @return Collection
 */
if (!function_exists('traverseTree')) {
    function traverseTree($collection,$relation,$descendants = '')
    {
        if($descendants == '') {
            $descendants = collect();
        }
        $descendants->push($collection);
        if(isset($collection->$relation)) {
            $descendants = traverseTree($collection->$relation,$relation,$descendants);
        }

        return $descendants;
    }
}

/** 
 * Remove Special Characters in given String
 *
 * @return String $string
 */
if (!function_exists('removeSpecialChar')) {
    function removeSpecialChar($string)
    {
        $search = ["'", "\"", "@"];
        $replace   = ["", "", ""];

        return str_replace($search, $replace, $string);
    }
}

/** 
 * Remove Email and Phone Number in given String
 *
 * @return String $message
 */
if (!function_exists('removeEmailNumber')) {
    function removeEmailNumber($message)
    {
        $replacement = "[removed]";

        $email_pattern = "/[^@\s]*@[^@\s]*\.[^@\s]*/";
        $message = preg_replace($email_pattern, $replacement, $message);

        $phone_pattern = "/\+?[0-9][0-9()\s+]{4,20}[0-9]/";
        $message = preg_replace($phone_pattern, $replacement, $message);

        $url_pattern = '/\b((https?|ftp|file):\/\/|www\.)[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i';
        $message = preg_replace($url_pattern, $replacement, $message);

        $number_pattern = "/[^A-Za-z\,\s]{5}/u";
        $message = preg_replace($number_pattern,'[censored]', $message);

        return $message;
    }
}

/**
 * format Translation Data
 *
 * @return Array
 */
if (!function_exists('formatTranslationData')) {
    function formatTranslationData($translations)
    {
        $return_data = [];
        foreach ($translations as $key => $value) {
            $return_data[] = array_merge(['locale' => $key],$value);
        }
        return collect($return_data);
    }
}

/**
 * Get Date in Given Format if format not given then return in default format
 *
 * @param string $date
 * @param string $format
 * @return String formatted date
 */
if (!function_exists('getDateInFormat')) {
    function getDateInFormat($date, $format = '')
    {
        if($format == '') {
            $format = DATE_FORMAT;
        }
        return date($format,strtotime($date));
    }
}

/**
 * Get Time in Given Format if format not given then return in default format
 *
 * @param string $time
 * @param string $format
 * @return String formatted time
 */
if (!function_exists('getTimeInFormat')) {
    function getTimeInFormat($time, $format = '')
    {
        if($format == '') {
            $format = TIME_FORMAT;
        }
        return date($format,strtotime($time));
    }
}

/**
 * Get Date in Given Format if format not given then return in default format
 *
 * @param string $date
 * @param string $format
 * @return String formatted date
 */
if (!function_exists('getDateTimeInFormat')) {
    function getDateTimeInFormat($date, $date_format = '', $time_format = '')
    {
        $time = getTimeInFormat($date,$time_format);
        $date = getDateInFormat($date,$date_format);
        return $date.' '.$time;
    }
}

/**
 * Get User Type
 *
 * @param string $hostUserId
 * @return Boolean
 */
if (!function_exists('getUserType')) {

    function getUserType($hostUserId)
    {
        return ($hostUserId == getCurrentUserId()) ? "Host" : "Guest";
    }
}

/**
 * Get Message Type
 *
 * @return Boolean
 */
if (!function_exists('getMessageType')) {

    function getMessageType($search,$type = 'name')
    {
        $message_types = resolve("MessageType");
        $message_type = $message_types->where($type,$search)->first();
        if($type == 'name') {
            return $message_type['id'];
        }
        return $message_type['name'];
    }
}

/**
 * Get Reservation Code
 *
 * @param string $hostUserId
 * @return Boolean
 */
if (!function_exists('getReserveCode')) {
    function getReserveCode($seed,$prefix = "",$length = 8)
    {  
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        mt_srand($seed);
        $code = "";
        for($i=0;$i<$length;$i++) {
            $code .= $codeAlphabet[mt_rand(0,strlen($codeAlphabet)-1)];
        }

        return $prefix.$code;
    }
}

/**
 * Get Host id
 * 
 */
if (!function_exists('getHostId')) {
    function getHostId() 
    {
        $user = getCurrentUser();
        if ($user != '') {
            return $user->user_type == 'sub_host' ? $user->host_id : $user->id;
        }
        return NULL;
    }
}

/**
 * Get Current User
 *
 */
if (!function_exists('getCurrentUser')) {
    function getCurrentUser()
    {
        $guard = 'web';
        if(request()->segment(1) == 'api') {
            $guard = 'api';
        }
        else if(request()->segment(1) == global_settings('admin_url')) {
            $guard = 'admin';
        }
        else if(request()->segment(1) == global_settings('host_url')) {
            $guard = 'host';
        }
        return \Auth::guard($guard)->user();
    }
}

/**
 * Get Current User ID
 *
 */
if (!function_exists('getCurrentUserId')) {
    function getCurrentUserId()
    {
        $user = getCurrentUser();
        return optional($user)->id;
    }
}

/**
 * Format pricing form
 *
 * @param  String $key
 * @param  String $value
 * @return  Array $pricing_form
 */
if(!function_exists('formatPricingForm')) {
    function formatPricingForm($form_data)
    {
        $value_prefix = $form_data['value_prefix'] ?? '';
        $pricing_form['key'] = $form_data['key'] ?? '';
        $pricing_form['value'] = $value_prefix.$form_data['value'];
        $pricing_form['description'] = $form_data['description'] ?? '';
        $pricing_form['count'] = $form_data['count'] ?? '';
        if(isset($form_data['no_prefix'])) {
            $pricing_form['value'] = $form_data['value'];
        }
        $pricing_form['class'] = $form_data['class'] ?? '';
        $pricing_form['tooltip'] = $form_data['tooltip'] ?? '';
        $pricing_form['dropdown'] = isset($form_data['dropdown']) ? true : '';
        $pricing_form['dropdown_values'] = $form_data['dropdown_values'] ?? [];
        $pricing_form['key_style'] = $form_data['key_style'] ?? '';
        $pricing_form['border'] = isset($form_data['border']) ? true : false;
        $pricing_form['value_style'] = $form_data['value_style'] ?? '';
        $pricing_form['new_line'] = $form_data['new_line'] ?? false;
        $pricing_form['type'] = $form_data['type'] ?? 'item';
        return $pricing_form;
    }
}

/**
 * update User Penalty
 *
 * @return Array
 */
if (!function_exists('updateUserPenalty')) {
    function updateUserPenalty($user_id, $currency_code, $amount)
    {
        $user_penalty = \App\Models\UserPenalty::firstOrNew(['user_id' => $user_id]);
        $user_penalty->user_id = $user_id;
        $user_penalty->total += currencyConvert($amount,$currency_code,$user_penalty->currency_code);
        $user_penalty->remaining += currencyConvert($amount,$currency_code,$user_penalty->currency_code);
        $user_penalty->currency_code = $user_penalty->currency_code;
        $user_penalty->save();
    }
}

/**
 * send Push Notification To User
 *
 * @return void
 */
if (!function_exists('sendNotificationToUser')) {
    function sendNotificationToUser($user,$notify_data)
    {
        $firebaseToken = [$user->fcm_token];
        
        $url = 'https://fcm.googleapis.com/fcm/send';
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $notify_data['title'],
                "body" => $notify_data['message'],
                'sound' => 'default',
                'badge' =>'1',
            ],
            "data" => $notify_data['data'] ?? [],
        ];

        try {
            $response = Http::withToken(credentials("server_key","Firebase"))->post($url,$data);
            return $response->successful();
        }
        catch(\Exception $e) {
            return false;
        }
    }
}

/** 
 * Return Unix timestamp from ical date time format 
 * 
 * @param {string} $icalDate A Date in the format YYYYMMDD[T]HHMMSS[Z] or
 *                           YYYYMMDD[T]HHMMSS
 *
 * @return {int} 
 */ 
if (!function_exists('iCalDateToUnixTimestamp')) {
    function iCalDateToUnixTimestamp($icalDate) 
    { 
        $icalDate = str_replace('T', '', $icalDate); 
        $icalDate = str_replace('Z', '', $icalDate); 

        $pattern  = '/([0-9]{4})';   // 1: YYYY
        $pattern .= '([0-9]{2})';    // 2: MM
        $pattern .= '([0-9]{2})';    // 3: DD
        $pattern .= '([0-9]{0,2})';  // 4: HH
        $pattern .= '([0-9]{0,2})';  // 5: MM
        $pattern .= '([0-9]{0,2})/'; // 6: SS
        preg_match($pattern, $icalDate, $date); 

        // Unix timestamp can't represent dates before 1970
        if ($date[1] <= 1970) {
            return false;
        } 
        // Unix timestamps after 03:14:07 UTC 2038-01-19 might cause an overflow
        // if 32 bit integers are used.
        $timestamp = mktime((int)$date[4], 
                            (int)$date[5], 
                            (int)$date[6], 
                            (int)$date[2],
                            (int)$date[3], 
                            (int)$date[1]);
        return  $timestamp;
    } 

}

/** 
 * create time range 
 *  
 * @param mixed $start start time 
 * @param mixed $end   end time
 * @param string $interval time intervals, 1 hour, 1 mins, 1 secs, etc.
 * @param string $format time format, e.g., 12 or 24
 */
if (!function_exists('generateTimeRange')) {
    function generateTimeRange($start, $end, $interval = '30 mins', $format = '12')
    {
        $startTime = strtotime($start); 
        $endTime   = strtotime($end);
        $returnTimeFormat = ($format == '12')?'h:i A':'H:i A';

        $current   = time();
        $addTime   = strtotime('+'.$interval, $current); 
        $diff      = $addTime - $current;

        $times = array();
        while ($startTime < $endTime) {
            $times[date('H:i:s',$startTime)] = date($returnTimeFormat, $startTime); 
            $startTime += $diff; 
        }
        if($end == "24:00") {
            $times[date('H:i:s',$startTime-60)] = date($returnTimeFormat, $startTime-60); 
        }
        return $times;
    }
}

/**
 * create Transaction for Admin
 *
 * @return Array
 */
if (!function_exists('createTransaction')) {
    function createTransaction($data)
    {
        $transaction = new \App\Models\Transaction;
        $transaction->list_type = $data['list_type'] ?? 'hotel';
        $transaction->user_id = $data['user_id'];
        $transaction->reservation_id = $data['reservation_id'];
        $transaction->type = $data['type'];
        $transaction->description = $data['description'];
        $transaction->currency_code = $data['currency_code'];
        $transaction->amount = $data['amount'];
        $transaction->transaction_id = $data['transaction_id'];
        $transaction->payment_method = $data['payment_method'];
        $transaction->save();
    }
}
