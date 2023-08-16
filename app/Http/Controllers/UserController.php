<?php

/**
 * User Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers
 * @category    UserController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Message;
use App\Models\Hotel;
use App\Models\Payout;
use App\Models\PayoutMethod;
use App\Models\PayoutMethodDetail;
use App\Models\Reservation;
use App\Models\Wishlist;
use App\Models\WishlistList;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Auth;
use Lang;
use Validator;
use Google_Client;

class UserController extends Controller
{
	/**
	* Check User Current Login User or not
	*
	* @param Int $id
	* @return Object
	*/
	protected function checkCurrentUser($id)
	{
		$return_data['status'] = false;
		if(Auth::id() != $id) {
			$return_data['status_message'] = Lang::get('messages.invalid_user');
			return $return_data;
		}
		$return_data['status'] = true;
		return $return_data;
	}

	/**
	* Display the user Dashboard
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  \App\Http\Controllers\InboxController  $inbox_controller
	* @return \Illuminate\Http\Response
	*/
	public function index(Request $request)
	{
		$currentDate = now();
        $reservation_controller = resolve("App\Http\Controllers\ReservationController");
        $max_date = now()->addDays(10)->format('Y-m-d 23:59:59');
        $reservations = Reservation::UserBased("User")
        	->with('hotel.hotel_address','user','host_user')
			->where('status','Accepted')
			->whereBetween('checkin',[date('Y-m-d'),$max_date])
			->get();
		$data['reservations'] = $reservation_controller->mapReservationsData($reservations,'user')->sortBy('reserve_date');
		
        return view('user.guest_dashboard', $data);
	}

	/**
	* Create the New User based on Email Signup
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function createUser(Request $request)
	{
		$password_rule = Password::min(8)->mixedCase()->numbers()->uncompromised();
		if(displayCrendentials()) {
			$password_rule = Password::min(8);
		}
		$rules = array(
			'email' => ['required', 'max:50', 'email'],
			'full_name' => ['required', 'max:60'],
			'city' => ['required'],
			'country' => ['required'],
			'password' => ['required', 'confirmed', $password_rule],
			'phone_number' => ['required', function ($attribute, $value, $fail) {
				if (substr($value, 0, 1) !== '0') {
					$fail('Phone Number is invalid. It should start with "0".');
				}
			}],
		);
		$attributes = array(
			'email' => Lang::get('messages.email'),
			'first_name' => Lang::get('messages.first_name'),
			'last_name' => Lang::get('messages.last_name'),
			'password' => Lang::get('messages.password'),
			'city' => Lang::get('messages.city'),
			'country' => Lang::get('messages.country'),
			'phone_number' => Lang::get('messages.phone_number'),
			'birthday_month' => Lang::get('messages.birthday').' '.Lang::get('messages.month'),
			'birthday_day' => Lang::get('messages.birthday').' '.Lang::get('messages.day'),
			'birthday_year' => Lang::get('messages.birthday').' '.Lang::get('messages.year'),
		);

		$user_email_count = User::authBased()->where('email',$request->email)->count();
        if($user_email_count > 0) {
            $rules['email'] =  ['required','max:50','email','unique:users'];
        }

		$validator = Validator::make($request->all(), $rules, [], $attributes);
		if ($validator->fails()) {
			// return back with popup_code 1 for show Signup popup
			return back()->withErrors($validator)->withInput()->with('popup_code', 1);
		}

		if(!empty($request->birthday_year) && !empty($request->birthday_month) && !empty($request->birthday_day)) {
			$dob = $request->birthday_year.'-'.$request->birthday_month.'-'.$request->birthday_day;

			if(!isValidDate($dob,'Y-n-j')) {
				$birthday_error = [
					'birthday_day' => Lang::get('messages.invalid_dob'),
				];
				return back()->withErrors($birthday_error)->withInput()->with('popup_code', 1);
			}

			if(!isAbove18($dob,'Y-n-j')) {
				$birthday_error = [
					'birthday_day' => Lang::get('messages.age_should_be_above_18'),
				];
				return back()->withErrors($birthday_error)->withInput()->with('popup_code', 1);
			}
		}
		
		if(checkEnabled('ReCaptcha')) {
			$recaptcha_service = resolve('App\Services\ReCaptchaService');
			$captchaValidate = $recaptcha_service->validateReCaptcha($request['g-recaptcha-response']);
			if(!$captchaValidate['status']) {
				$recaptcha_error = [
					'g-recaptcha-response' => Lang::get('messages.please_complete_captcha_to_continue')
				];
				return back()->withErrors($recaptcha_error)->withInput()->with('popup_code', 1);
			}
		}

		$user_data = $request->only(['email','password','phone_number','address_line_1','address_line_2','state','postal_code','gender']);
		$name = explode(' ',$request->full_name);
		$user_data['first_name'] = $name[0] ?? '';
		$user_data['last_name'] = $name[1] ?? '';
		$user_data['dob'] = $dob ?? NULL;

		$country = resolve('Country')->where('name',$request->country)->first();
		$city = resolve('City')->where('name',$request->city)->first();
		
		$ip_data = getIpBasedData($_SERVER['REMOTE_ADDR']);
		$user_data['timezone'] = $ip_data['timezone'];
		$user_data['user_currency'] = $ip_data['currency_code'];
		$user_data['country_code'] = optional($country)->name;
		$user_data['city'] = optional($city)->name;
		$user_data['phone_code'] = $country->phone_code;
		$user_data['status'] = 'active';
		$auth_service = resolve("App\Services\AuthUser\AuthViaEmail");
		$user = $auth_service->createOrGetUser($user_data);

		$credentials = $request->only(['email', 'password']);
		$credentials['user_type'] = function($query) {
            $query->authBased();
        };
		if($auth_service->attemptLogin($credentials)) {
        	$auth_service->completeVerification(Auth::id(),$user->id);
			if(checkEnabled('Firebase')) {
				$firebase_service = resolve("App\Services\FirebaseService");
				$token = $firebase_service->createCustomToken(Auth::user()->email);
				session(['firebase_auth_token' => $token]);
			}

			flashMessage('success', Lang::get('messages.success'), Lang::get('messages.user_register_successful'));
			if(session('ajax_redirect_url')) {
				return redirect()->intended(session('ajax_redirect_url'));
			}
			$redirect_url = resolveRoute('dashboard');
        	return redirect($redirect_url);
		}
		flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.login_failed'));
		$redirect_url = resolveRoute('login');
        return redirect($redirect_url);
	}

	/**
	* Complete Social Signup
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function completeSocialSignup(Request $request)
	{
		$auth_type = $request->auth_type;
		$redirect_route = Auth::check() ? resolveRoute('update_account_settings',['page' => 'login-and-security']) : resolveRoute('login');
		
		if(!in_array($auth_type, ['Google','Facebook','Apple'])) {
			flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.invalid_request'));
			return redirect($redirect_route);
		}
		
		if($auth_type == 'Google') {
			$client = new Google_Client(['client_id' => credentials('client_id','Google')]);
			$payload = $client->verifyIdToken($request->id_token);
			if ($payload) {
				$user_data['google_id'] = $auth_id = $payload['sub'];
			}
			else {
				flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.invalid_request'));
				return redirect($redirect_route);
			}

			$user_data['first_name'] = $payload['given_name'] ?? $auth_id;
			$user_data['last_name'] =  $payload['family_name'] ?? '';
			$user_data['user_language'] =  $payload['locale'] ?? '';
			$user_data['profile_picture'] =  substr($payload['picture'], 0, strpos($payload['picture'], "=s"));
			$user_data['email'] = ($payload['email'] == '') ? $auth_id.'@gmail.com' : $payload['email'];
		}
		
		if($auth_type == 'Facebook') {
			try {
				$api_url = "https://graph.facebook.com/me";
				$queryParams = [
					"access_token" => $request->access_token,
					"appsecret_proof" => hash_hmac('sha256', $request->access_token, credentials('app_secret','Facebook')),
					"fields" => 'id,first_name,last_name,email',
				];
				$response = Http::get($api_url,$queryParams);
				$response_data = $response->json();
				if($response->failed()) {
					$error_message = isset($response_data['error']) ? $response_data['error']['message'] : Lang::get('messages.something_went_wrong');
					flashMessage('danger', Lang::get('messages.failed'), $error_message);
					return redirect($redirect_route);
				}

				$user_data = $response_data;
				$user_data['facebook_id'] = $auth_id = $response_data['id'];
				$user_data['profile_picture'] = "https://graph.facebook.com/".$auth_id."/picture?type=large";
				unset($user_data['id']);
			}
			catch(\Exception $exception) {
				flashMessage('danger', Lang::get('messages.failed'), $exception->getMessage());
				return redirect($redirect_route);
			}
		}

		$auth_service = resolve("App\Services\AuthUser\AuthVia".$auth_type);
		
		if(Auth::check()) {
			$user = User::where('id','!=',Auth::id())->where(function($query) use ($user_data,$auth_type) {
				$query->where('email',$user_data['email'])->orWhere('email',$user_data[strtolower($auth_type).'_id']);
			})->count();
			if($user > 0) {
				flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.already_connected_with_other',['replace_key_1' => $auth_type]));
				$redirect_url = resolveRoute('update_account_settings',['page' => 'login-and-security']);	
        		return redirect($redirect_url);
			}
			$auth_service->completeVerification(Auth::id(),$auth_id);

			flashMessage('success', Lang::get('messages.success'), Lang::get('messages.social_import_successful',['replace_key_1' => $auth_type]));
			$redirect_url = resolveRoute('update_account_settings',['page' => 'login-and-security']);
        	return redirect($redirect_url);
		}

		$ip_data = getIpBasedData($_SERVER['REMOTE_ADDR']);
		$user_data['timezone'] = $ip_data['timezone'];
		$user_data['user_currency'] = $ip_data['currency_code'];
		$user_data['country_code'] = $ip_data['country_code'];
		
		$user = $auth_service->createOrGetUser($user_data);
		if($auth_service->attemptLogin($user->only('id'))) {
			$user->last_active_at = now();
			$user->save();
			$redirect_url = resolveRoute('dashboard');
        	return redirect($redirect_url);
		}

		flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.social_login_failed'));
		return redirect($redirect_route);
	}

	/**
	* disconnect Social Account
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function disconnectSocialAccount(Request $request)
	{
		$auth_type = $request->auth_type;
		if(!in_array($auth_type, ['Google','Facebook','Linkedin'])) {
			flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.invalid_request'));
			return back();
		}
		
		$auth_service = resolve("App\Services\AuthUser\AuthVia".$auth_type);
		$auth_service->diconnectVerification(Auth::id());

		flashMessage('success', Lang::get('messages.success'), Lang::get('messages.social_disconnected_reconnect_again'));
		$redirect_url = resolveRoute('update_account_settings',['page' => 'login-and-security']);
        return redirect($redirect_url);
	}

	/**
	* Authenticate User with given credentials
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function authenticate(Request $request)
	{
		$recaptcha_enabled = global_settings('recaptcha_enabled');
		$rules = array(
			'login_email'     => 'required|email',
			'login_password'  => 'required',
		);
		$attributes = array(
			'login_email'     => Lang::get('messages.email'),
			'login_password'  => Lang::get('messages.password'),
		);
		$validator = Validator::make($request->all(), $rules, [], $attributes);

		if ($validator->fails()) {
			// return back with popup_code 2 for show Login popup
			return back()->withErrors($validator)->withInput()->with('popup_code', 2);
		}

		if(checkEnabled('ReCaptcha')) {
			$recaptcha_service = resolve('App\Services\ReCaptchaService');
			$captchaValidate = $recaptcha_service->validateReCaptcha($request['g-recaptcha-response']);
			if(!$captchaValidate['status']) {
				$recaptcha_error = [
					'g-recaptcha-response' => Lang::get('messages.please_complete_captcha_to_continue')
				];
				return back()->withErrors($recaptcha_error)->withInput()->with('popup_code', 2);
			}
		}

		$auth_service = resolve("App\Services\AuthUser\AuthViaEmail");
		$credentials['email'] = $request->login_email;
		$credentials['password'] = $request->login_password;
		$remember_me = ($request->remember_me == "1");
		$credentials['user_type'] = function($query) {
            $query->authBased();
        };
		
		if($auth_service->attemptLogin($credentials,$remember_me)) {
			if(Auth::user()->status == 'inactive') {
				Auth::logout();
				flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.your_account_is_disabled'));
				return back();
			}

			if(checkEnabled('Firebase')) {
				$firebase_service = resolve("App\Services\FirebaseService");
				$token = $firebase_service->createCustomToken(Auth::user()->email);
				session(['firebase_auth_token' => $token]);
			}

			Auth::user()->last_active_at = now();
			Auth::user()->save();

			$intented_url = session('url.intended');
			$has_admin_url = Str::contains($intented_url,global_settings('admin_url'));
			$has_host_url = \Str::contains($intented_url,global_settings('host_url'));
			if($intented_url != '' && !$has_admin_url && !$has_host_url) {
				return redirect($intented_url);
			}
			$redirect_url = resolveRoute('dashboard');
        	return redirect($redirect_url);
		}

		flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.login_failed'));
		$redirect_url = resolveRoute('login');
        return redirect($redirect_url);
	}

	/**
	 * Number Verification
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function authenticateMobile(Request $request)
	{
		$rules = array(
			'login_country_code'=> 'required|exists:countries,name',
			'login_phone_number'=> 'required|min:6|regex:/^[0-9]+$/',
			'type'=> 'required|in:send_otp,verify_otp',
		);
		$attributes = array(
			'login_phone_number' => Lang::get('messages.phone_number'),
		);
		$validator = Validator::make($request->all(), $rules, [], $attributes);
		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'status_message' => $validator->messages()->first(),
			]);
		}

		$country = resolve('Country')->where('name',$request->login_country_code)->first();
		
		$user = User::where('phone_number',$request->login_phone_number)->where('phone_code',$country->phone_code)->first();
		if($user == '') {
			return response()->json([
				'status' => false,
				'status_message' => Lang::get('messages.your_phone_number_does_not_match'),
			]);
		}

		if($user->status == 'inactive') {
			return response()->json([
				'status' => false,
				'status_message' =>Lang::get('messages.your_account_is_disabled'),
			]);
		}

		if($request->type == 'send_otp') {
			$number = $country->phone_code.$request->login_phone_number;
			$sms_service = resolve("App\Contracts\SmsGateway");
			$auth_code = Str::random(6);
			$verify_code = rand(100000,999999);
			$data = [
				'text' => Lang::get('messages.your_verification_code_is',['replace_key_1' => SITE_NAME]).' '.$verify_code.'. '.Lang::get('messages.dont_share_with_anyone'),
			];
			$result = $sms_service->send($number,$data);
			if($result['status']) {
				$verify_data = [
					'code' => $verify_code,
					'phone_code' => $country->phone_code,
					'country_code' => $request->login_country_code,
					'phone_number' => $request->login_phone_number,
				];
				session(['login_verify_data_'.$auth_code => $verify_data]);
				if(displayCrendentials()) {
					$result['verify_code'] = $verify_code;
				}
				$result['auth_code'] = $auth_code;
				$result['status_message'] = Lang::get('messages.we_texted_code_to_user',['replace_key_1' => $number]);
			}
			return $result;
		}

		if($request->type == 'verify_otp') {
			$verify_data = session('login_verify_data_'.$request->auth_code);
			if(!$verify_data) {
				return response()->json([
					'status' => false,
					'status_message' => Lang::get('messages.we_were_unable_to_validate_phone_number'),
				]);
			}
			if($verify_data['code'] == $request->code) {
				if(Auth::loginUsingId($user->id, TRUE)) {
					$redirect_url = session('url.intended');
					$has_admin_url = Str::contains($redirect_url,global_settings('admin_url'));
					if($redirect_url == '' || !$has_admin_url) {
						$redirect_url = resolveRoute('dashboard');
					}

					return response()->json([
						'status' => 'redirect',
						'redirect_url' => $redirect_url,
					]);
				}
			}

			return response()->json([
				'status' => false,
				'invalid_otp' => true,
				'status_message' => Lang::get('messages.invalid_otp'),
			]);
		}

		return response()->json([
			'status' => false,
			'status_message' => Lang::get('messages.we_were_unable_to_validate_phone_number'),
		]);
	}

	/**
	* Verify User Email
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function verifyUserEmail(Request $request)
	{
		$user = User::findOrFail($request->id);
        $redirect_url = resolveRoute('home');

		if (! hash_equals((string) $request->id, (string) $user->getKey())) {
            flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.authorization_error'));
        	return redirect($redirect_url);
        }

        if (! hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.authorization_error'));
        	return redirect($redirect_url);
        }
        $user_verification = $user->user_verification;

        if ($user_verification->email) {
        	flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.email_already_verified'));
        	return redirect($redirect_url);
        }

        $user_verification->email = 1;
        $user_verification->save();

        flashMessage('success', Lang::get('messages.success'), Lang::get('messages.email_verified_successfully'));
        return redirect($redirect_url);
	}

	/**
	* Reset Password of the User
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function resetPassword(Request $request)
	{
		if($request->isMethod("POST")) {
			$user = User::where('email',$request->email)->first();
			if($user == '') {
				flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.user_not_exists'));
				$redirect_url = resolveRoute('signup');
        		return redirect($redirect_url);
			}
			
		    resolveAndSendNotification("resetUserPassword",$user->id);
		    flashMessage('success', Lang::get('messages.success'), Lang::get('messages.reset_link_sent_to_mail'));
		    $redirect_url = resolveRoute('login');
        	return redirect($redirect_url);
		}
		$broker = app('auth.password.broker');
		$user = $broker->getUser($request->all());
		if($user == '') {
			flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.user_not_exists'));
			$redirect_url = resolveRoute('signup');
        	return redirect($redirect_url);
		}
		if($broker->tokenExists($user,$request->token)) {
			$data['email'] = $request->email;
			$data['reset_token'] = $request->token;
			return view('set_password',$data);
		}
		flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.link_expired'));
		$redirect_url = resolveRoute('login');
        return redirect($redirect_url);
	}

	/**
	* Update New Password to the User
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function setNewPassword(Request $request)
	{
		$password_rule = Password::min(8)->mixedCase()->numbers()->uncompromised();
		if(displayCrendentials()) {
			$password_rule = Password::min(8);
		}

		$rules = array(
			'password' => ['required',$password_rule,'confirmed'],
		);

		$request->validate($rules);
		
		$broker = app('auth.password.broker');
		$user = $broker->getUser($request->only(['email']));
		if(!$broker->tokenExists($user,$request->reset_token)) {
			flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.invalid_request'));
			$redirect_url = resolveRoute('login');
        	return redirect($redirect_url);
		}
		$user->password = $request->password;
		$user->save();
		
		$broker->deleteToken($user);

		Auth::LoginUsingId($user->id);

		flashMessage('success', Lang::get('messages.success'), Lang::get('messages.password_updated_successfully'));
		$redirect_url = resolveRoute('dashboard');
        return redirect($redirect_url);
	}

	/**
	* Show User Account Related Settings
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function accountSettings()
	{
		return redirect()->route('update_account_settings',['page' => 'personal-information']);
		$user = Auth::user();
		$user->load('user_verification');
		return view('user.account_settings',compact('user'));
	}

	/**
	* Update User Profile
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function updateProfile(Request $request)
	{
		$rules = array(
			'user_id' => 'required',
			'phone_number' => 'integer',
		);
		$attributes = array('user_id' => 'User ID');
		$request->validate($rules,[],$attributes);
		$validateUser = $this->checkCurrentUser($request->user_id);
		
		if(!$validateUser['status']) {
			return response()->json([
				'status' => false,
				'status_message' => $validateUser['status_message'],
			]);
		}

		$user = Auth::user();
		$user_data = array();
		$user->append('user_document_src','formatted_created_at');

		if($request->data_from == 'view_profile') {
			$user_info = $user->user_information;
			$user_info->about = $request->about;
			$user_info->work = $request->work;
			$user_info->location = $request->location;
			$user_info->languages = implode(',',$request->language);
			$user_info->save();
		}

		if($request->data_from == 'personal_info') {
			if($request->data_type == 'legal_name') {
				if($request->first_name ==='' && $request->last_name ==='')
				{
					$user_data = $request->only(['first_name','last_name']);
				}
			}
			
			if($request->data_type == 'gender') {
				if(($request->gender && (!$user->user_information->gender)))
				{
					$user_info = $user->user_information;
					$user_info->gender = $request->gender;
					$user_info->save();
				}
			}
			
			if($request->data_type == 'dob') {
				if($user->dob == null)
				{
					$date = $request->birthday_year.'-'.$request->birthday_month.'-'.$request->birthday_day;
					if(isValidDate($date,'Y-n-j')) {
						$user_info = $user->user_information;
						$user_info->dob = $date;
						$user_info->save();
					}

				}
			}
			
			if($request->data_type == 'email_addr') {
				if($request->email ==='')
				{
					$user_data = $request->only(['email']);
				}
			}
			if($request->data_type == 'address') {
				$user_info = $user->user_information;
				if($user->user_information->address_line_1==='')
				{
					$user_info->address_line_1 = $request->address_line_1;
				}
				if($user->user_information->address_line_2==='')
				{
					$user_info->address_line_2 = $request->address_line_2;
				}
				if($user->user_information->city==='')
				{
					$user_info->city = $request->city;
				}
				if($user->user_information->state==='')
				{
					$user_info->state = $request->state;
				}
				if($user->user_information->country_code==='')
				{
					$user_info->country_code = $request->country_code;
				}
				if($user->user_information->postal_code==='')
				{
					$user_info->postal_code = $request->postal_code;
				}
				$user_info->save();

				$user_data = $request->only(['city','country_code']);
			}
			if($request->data_type == 'phone_number') {
				if($user->phone_number==='')
				{
					$user_data = $request->only(['phone_number','phone_code','country_code']);
				}
			}
			if($request->data_type == 'user_language') {
				$user_data = $request->only(['user_language']);
			}

			if($request->data_type == 'user_currency') {
				$user_data = $request->only(['user_currency']);
			}
			
			if($request->data_type == 'timezone') {
				$user_data = $request->only(['timezone']);
			}

			$user->update($user_data);
			$user->load('user_verification');
			$user->append('user_language_name');
		}

		if($request->data_from == 'login_and_security') {
			$user->append('has_signup_with_email');
			if($request->data_type == 'password') {
				$password_rule = Password::min(8)->mixedCase()->numbers()->uncompromised();
				if(displayCrendentials()) {
					$password_rule = Password::min(8);
				}
				$rules = array(
					'current_password' => 'required|current_password',
					'password' => ['required',$password_rule,'confirmed'],
				);

				$validator = Validator::make($request->all(), $rules, [], $attributes);
				if ($validator->fails()) {
					return response()->json([
						'status' => false,
						'error_text' => Lang::get('messages.failed'),
						'error_message' => $validator->messages(),
					]);
				}
				if(!Hash::check($request->current_password,$user->password)){
					return response()->json([
						'status' => false,
						'user' => $user->user_information->append('language_array'),
						'error_text' => Lang::get('messages.failed'),
						'error_message' => Lang::get('messages.current_password_does_not_match'),
					]);
					
				}
				$user_data['password'] = $request->new_password;
		        resolveAndSendNotification("userActivity",$user->id,['type' => 'password_changed']);
			}
			$user->update($user_data);
		}

		if($request->data_type == 'user_language') {
			if(global_settings('is_locale_based')) {
				$url = Str::of(url()->previous())->replace(url('/'), '')->replaceFirst(LOCALE,$request->user_language)->prepend(url('/'));
			}
			else {
				$url = resolveRoute('update_account_settings',['page' => 'site-setting']);
			}
			return response()->json([
				'status' => 'redirect',
				'redirect_url' => $url,
			]);
		}

		$user->user_information->append('language_array');
		$user->user_information->append('address');
		$user->append('profile_picture_src');
		
		return response()->json([
			'status' => true,
			'user' => $user,
			'status_text' => Lang::get('messages.success'),
			'status_message' => Lang::get('messages.updated_successfully'),
		]);
	}

	/**
	* Display the user Profile
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function viewProfile(Request $request)
	{
		$id = $request->id;
		$user = $id == Auth::id() ? Auth::user() : User::findOrFail($id);
		
		$wishlists = Wishlist::with('wishlist_lists')->where('user_id',$id);
		if($id != Auth::id()) {
			$wishlists = $wishlists->where('privacy',1);
		}

		$hotel_wishlists = $wishlists->get()->map(function($wishlist) {
			$wishlist_lists = $wishlist->wishlist_lists->where('list_type','hotel');
			$wishlist_lists = $wishlist_lists->map(function($wishlist_list) {
				$hotel = Hotel::findOrFail($wishlist_list->list_id);
				$hotel_data = $hotel->only(['id','name','room_type_name','cancellation_policy','status','total_rating','link']);
	            $hotel_data['rating'] = floatval($hotel->rating);
	            $hotel_data['is_saved'] = 1;

	            $location_data = $hotel->hotel_address->only(['latitude','longitude','city','state','country_name']);
	            
	            $price_data['price'] = $hotel->price;
	            $price_data['currency_symbol'] = $hotel->currency_symbol;
	            // $price_data['guests_text'] = $room->guests.' '. Lang::choice('messages.listing.guest',$room->guests);
	            // $price_data['room_type_text'] = $room->room_type_name;
	            $price_data['price_text'] = $hotel->currency_symbol.' '.$hotel->price.' '. Lang::get('messages.per_night');
	            $price_data['image_src'] = $hotel->hotel_photos->first()->image_src;

	            return array_merge($hotel_data,$location_data,$price_data);
			});

			return [
				'id' => $wishlist->id,
				'user_id' => $wishlist->user_id,
				'name' => $wishlist->name,
				'privacy' => $wishlist->privacy,
				'thumbnail' => $wishlist->thumbnail,
				'list_count' => $wishlist->list_count,
				'wishlist_lists' => $wishlist_lists,
			];
		})
		->where('list_count','>','0')
		->values();

		$user->load('user_verification','reviews','user_information');
		$review_count = $user->reviews->count();
		$user->user_information->append('language_array');
		$hotels = Hotel::viewOnly()->with('hotel_address')->where('user_id',$id)->get();
		$meta_data = resolve('Meta');
        $page_data = $meta_data->where('route_name',\Route::currentRouteName())->first();
        $replace_keys = ['{USER_NAME}'];
        $replace_values = [$user->first_name];
        $title = Str::of($page_data->title)->replace($replace_keys,$replace_values);

		$experience_review_count = 0;
		$experience_wishlists = [];
		$experiences = [];

		return view('user.profile',compact('user','review_count','hotels','title','hotel_wishlists'));
	}

	/**
	* Update Account Related Data
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function updateAccountSettings(Request $request)
	{
		$user = Auth::user();
		$user->append('has_signup_with_email','profile_picture_src','user_document_src','formatted_created_at');
		$page = str_replace('-', '_', $request->page);
		$user->load('user_verification');
		$user->append('user_language_name');
		$user->user_information->append('address');
		
		$all_countries = resolve("Country");
		$data = compact('user','page','all_countries');

		if($page == 'payment_payouts') {
			$user->load('payout_methods');
		}
		else if($page == 'site_setting') {
			$data['timezones'] = \App\Models\Timezone::get()->pluck('name','value');
		}
		return view('user.update_profile',$data);
	}

	/**
	* Number Verification
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function numberVerification(Request $request)
	{
		$rules = array(
			'user_id' => 'required',
			'country_code' => 'required|exists:countries,name',
			'phone_number' => 'required|min:6|regex:/^[0-9]+$/|unique:users,phone_number,'.$request->user_id,
		);
		$attributes = array('user_id' => 'User ID');
		$messages = array('phone_number' => Lang::get('messages.please_enter_valid_phone_number'));

		$validator = Validator::make($request->all(), $rules,[],$attributes);
		
		if($validator->fails()) {
			return response()->json([
				'status' => false,
				'status_message' => $validator->messages()->first(),
			]);
		}

		$validateUser = $this->checkCurrentUser($request->user_id);
		
		if(!$validateUser['status']) {
			return response()->json([
				'status' => false,
				'status_message' => $validateUser['status_message'],
			]);
		}

		if($request->type == 'send_otp') {
			$country = resolve("Country")->where('name',$request->country_code)->first();
			$number = $country->phone_code.$request->phone_number;
			$sms_service = resolve("App\Contracts\SmsGateway");
			$verify_code = rand(100000,999999);
			$data = [
				'text' => Lang::get('messages.your_verification_code_is',['replace_key_1' => SITE_NAME]).' '.$verify_code.'. '.Lang::get('messages.dont_share_with_anyone'),
			];
			$result = $sms_service->send($number,$data);
			if($result['status']) {
				$verify_data = [
					'code' => $verify_code,
					'phone_code' => $country->phone_code,
					'country_code' => $country->name,
					'phone_number' => $request->phone_number,
				];
				session(['verify_data_'.Auth::id() => $verify_data]);
				if(displayCrendentials()) {
					$result['verify_code'] = $verify_code;
				}
				$result['status_message'] = Lang::get('messages.we_texted_code_to_user',['replace_key_1' => $number]);
			}
			return $result;
		}
		if($request->type == 'verify_otp') {
			$verify_data = session('verify_data_'.Auth::id());
			if(!$verify_data) {
				return response()->json([
					'status' => false,
					'status_message' => Lang::get('messages.we_were_unable_to_validate_phone_number'),
				]);
			}
			if($verify_data['code'] == $request->verification_code) {
				$user = Auth::user();
				$user->country_code = $verify_data['country_code'];
				$user->phone_code = $verify_data['phone_code'];
				$user->phone_number = $verify_data['phone_number'];
				$user->save();
				
				$user->load('user_verification');
				$user_verification = $user->user_verification;
				$user_verification->phone_number = 1;
				$user_verification->save();
				$user->append('user_language_name');
				$user->user_information->append('language_array');
				return [
					'status' => true,
					'user' => $user,
					'status_message' => Lang::get('messages.updated_successfully'),
				];
			}
			return [
				'status' => false,
				'status_message' => Lang::get('messages.we_were_unable_to_validate_phone_number'),
			];
		}
	}

	/**
	* Get Transaction History
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function transactionHistory(Request $request)
	{
        $start_date	= date('Y-m-d H:i:s',strtotime($request->start_year.'-'.$request->start_month.'-1 00:00:00'));
        $end_date = date('Y-m-t H:i:s',strtotime($request->end_year.'-'.$request->end_month.'-1 23:59:59'));

        $reservations = Reservation::where('user_id',Auth::id())->get()->map(function($reservation) {
            $user = $reservation->user;
            $host = $reservation->host;
            $host = $reservation->host;
            $hotel = $reservation->hotel;
            $hotel_address = $hotel->hotel_address;
            $room_reservations = $reservation->room_reservations;
            $adults = $room_reservations->sum('adults');
            $children = $room_reservations->sum('children');
            $total = $room_reservations->sum('total');
            $extra_adult = 0;
            $extra_adult_charge = 0;
            $room_reservation = $room_reservations->first();
            if($adults > $room_reservation->hotel_room->sum('adults')) {
                $extra_adult = $adults - $room_reservation->hotel_room->sum('adults');
                $extra_adult_charge = "0";
            }

            $extra_children = 0;
            $extra_children_charge = 0;
            $room_reservation = $room_reservations->first();
            if($adults > $room_reservation->hotel_room->sum('children')) {
                $extra_children = $children - $room_reservation->hotel_room->sum('children');
                $extra_children_charge = "0";
            }

            $extra_beds = 0;
            $extra_beds_charge = 0;
            $room_reservation = $room_reservations->first();
            if($adults > $room_reservation->hotel_room->sum('beds')) {
                $extra_beds = $beds - $room_reservation->hotel_room->sum('beds');
                $extra_beds_charge = "0";
            }

            $reservation_status = $reservation->status;
            if($reservation_status == 'Cancelled') {
                $reservation_status = $reservation->cancelled_by == 'Guest' ? 'Cancel by User' : 'Cancel by Property';
            }
            
            return [
                'property_id' => $hotel->id,
                'property_name' => $hotel->name,
                'property_star_rating' => $hotel->star_rating,
                'property_type' => $hotel->property_type_name,
                'property_telephone_number' => $hotel->tele_phone_number,
                'property_ext_number' => $hotel->extension_number,
                'property_fax_number' => $hotel->fax_number,
                'property_address' => $hotel_address->address_line_1,
                'ward' => $hotel_address->address_line_2,
                'city' => $hotel_address->city,
                'state' => $hotel_address->state,
                'country' => $hotel_address->country_name,
                'postal_code' => $hotel_address->postal_code,
                'property_website' => $hotel->website,
                'property_email' => $hotel->contact_email,
                'booking_confirmation_code' => $reservation->code,
                'booking_made_on' => $reservation->created_at->format(DATE_FORMAT.' '.TIME_FORMAT),
                'room_category' => $hotel->property_type_name,
                'check_in_date' => $reservation->formatted_checkin,
                'check_in_time' => $reservation->getTimingText('checkin_at'),
                'check_out_date' => $reservation->formatted_checkout,
                'check_out_time' => $reservation->getTimingText('checkout_at'),
                'adults' => $adults,
                'children' => $children,
                'room_rate_per_night' => numberFormat($total / $reservation->total_nights),
                'total_room_charges' => $reservation->currency_symbol.$reservation->total,
                'total_room_nights' => $reservation->total_nights,
                'number_of_extra_adults' => $extra_adult,
                'total_extra_adult_charges' => $reservation->currency_symbol.$extra_adult_charge,
                'number_of_extra_children' => $extra_children,
                'total_extra_children_charges' => $reservation->currency_symbol.$extra_children_charge,
                'number_of_extra_beds' => $extra_beds,
                'total_extra_beds_charges' => $reservation->currency_symbol.$extra_beds_charge,
                'total_extra_charges' => $reservation->currency_symbol.($extra_beds_charge+$extra_children_charge+$extra_adult_charge),
                'meal_plan' => $room_reservation->sum('meal_plan'),
                'discount_amount' => $reservation->currency_symbol.$reservation->coupon_price,
                'payment_method' => $reservation->payment_method,
                'total' => $reservation->sub_total,
                'total_amount' => $reservation->currency_symbol.$reservation->sub_total,
                'property_tax' => $reservation->currency_symbol.$reservation->property_tax,
                'property_service_charge' => $reservation->currency_symbol.$reservation->service_charge,
                'duhiviet_service_fee' => $reservation->currency_symbol.$reservation->service_fee,
                'grand_total' => $reservation->currency_symbol.$reservation->total,
                'property_policy' => $hotel->hotel_policy,
                'status' => $reservation_status,
            ];
        });
        
        /*$transactions = Transaction::where('user_id',Auth::id())->get()->map(function($transaction){
        	return [
        		'date' => $transaction->created_at->format(DATE_FORMAT),
        		'type' => ucfirst($transaction->type),
        		'payment_method' => $transaction->payment_method,
        		'confirmation_code' => $transaction->hotel_reservation->code,
        		'amount' => $transaction->amount,
        		'amount_text' => $transaction->currency_symbol.$transaction->amount,
        	];
        });*/

        return response()->json([
        	'status' => true,
        	'status_message' => Lang::get('messages.listed_successfully'),
        	'data' => $reservations,
        	'summary_amount' => numberFormat($reservations->sum('total')),
        ]);
	}

	/**
	* Update User Profile Picture
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function updatePhoto(Request $request)
	{
		$user = Auth::user();
		
		if (!$request->hasFile('file')) {
			return ['status' => false];
		}

		$profile_picture = $request->file("file");
		$image_handler = resolve('App\Contracts\ImageHandleInterface');
		$image_data['name_prefix'] = 'user_'.$user->id;
		$image_data['add_time'] = false;
		$image_data['target_dir'] = $user->getUploadPath();
		$image_data['image_size'] = $user->getImageSize();

		if(DELETE_STORAGE && $user->src != '' && $user->photo_source == 'site') {
            $user->deleteImageFile();
        }
		
		$upload_result = $image_handler->upload($profile_picture,$image_data);
		
		if($upload_result['status']) {
			$user->src = $upload_result['file_name'];
			$user->photo_source = 'site';
			$user->upload_driver = $upload_result['upload_driver'];
			$user->save();
			flashMessage('success', Lang::get('messages.success'), $upload_result['status_message']);
		}
		else {
			flashMessage('danger', Lang::get('messages.failed'), $upload_result['status_message']);
		}

		return ['status' => $upload_result['status'], "src" => $user->profile_picture_src];
	}

	/**
	* Update User Profile Picture
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function uploadUserDocument(Request $request)
	{
		$user = Auth::user();
		
		if (!$request->hasFile('file')) {
			return ['status' => false];
		}
		$profile_picture = $request->file("file");
		$image_handler = resolve('App\Contracts\ImageHandleInterface');
		$image_data['name_prefix'] = 'user_document_'.$user->id;
		$image_data['add_time'] = false;
		$image_data['target_dir'] = $user->getUploadPath();
		$image_data['image_size'] = $user->getImageSize();
		if(DELETE_STORAGE && $user->documnet_src != '' && $user->photo_source == 'site') {
            $image_data['name'] = $user->documnet_src;
            $handler = $user->getImageHandler();
            $handler->destroy($image_data);
        }
		
		$upload_result = $image_handler->upload($profile_picture,$image_data);
		
		if($upload_result['status']) {
			$user->document_src = $upload_result['file_name'];
			$user->photo_source = 'site';
			$user->upload_driver = $upload_result['upload_driver'];
			$user->verification_status = 'Pending';
			$user->save();
			flashMessage('success', Lang::get('messages.success'), $upload_result['status_message']);
		}
		else {
			flashMessage('danger', Lang::get('messages.failed'), $upload_result['status_message']);
		}

		return ['status' => $upload_result['status'], "src" => $user->user_document_src];
	}

	/**
	* remove User Profile Picture
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function removePhoto(Request $request)
	{
		$user = Auth::user();
		
		if(DELETE_STORAGE && $user->src != '' && $user->photo_source == 'site') {
			$user->deleteImageFile();
        }

		$user->src = '';
		$user->photo_source = 'site';
		$user->upload_driver = "Local";
		$user->save();


		return ['status' => true, "src" => $user->profile_picture_src];
	}

	/**
	* Logout Current User
	*
	* @return \Illuminate\Http\Response
	*/
	public function logout()
	{
		session()->forget('url.intended');
		Auth::logout();
		$redirect_url = resolveRoute('login');
        return redirect($redirect_url);
	}
}