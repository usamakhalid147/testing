<?php

/**
 * Home Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Host
 * @category    HomeController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Transaction;
use App\Models\Reservation;
use App\Models\Payout;
use App\Models\UserPenalty;
use App\Models\Country;
use App\Models\Admin;
use App\Models\LoginSlider;
use App\Models\Company;
use Carbon\Carbon;
use Lang;
use Auth;
use Illuminate\Support\Facades\Storage;


class HomeController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.dashboard');
        $this->view_data['active_menu'] = 'dashboard';
    }

    /**
     * Display a hotel of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->isMethod("POST")) {
            if(!empty($request->month) && !empty($request->year)) {
                return $this->getBarChartData($request->month,$request->year);
            }
            return $this->getDashboardData($request->year);
        }
        $today = Carbon::today();
        $year = $today->format('Y');
        $month = $today->format('m');
        $this->view_data['bar_chart'] = $this->getBarChartData($month,$year);
        $this->view_data['dashboard_data'] = $this->getDashboardData($year);
        $hotels = Hotel::with('hotel_address','hotel_photos')->latest()->limit(5)->get();
        
        $reservations = Reservation::userBased()->latest()->limit(5)->get();

        $this->view_data['hotels'] = Hotel::authUser()->count();
        $this->view_data['reservations'] = Reservation::userBased()->count();
        $this->view_data['today_reservation'] = Reservation::userBased()->where('checkin',$today->format('Y-m-d'))->count();
        $total_earnings = 0;
        Payout::authUser()->get()->map(function($payout) use(&$total_earnings) {
            $total_earnings += $payout->amount;
        });
        $this->view_data['total_earnings'] = $total_earnings;

        $this->view_data['recent_reservations'] = $reservations->map(function($reservation) {
            return [
                'id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'hotel_id' => $reservation->hotel_id,
                'hotel_name' => $reservation->hotel->name,
                'location' => $reservation->hotel->hotel_address->address_line_display,
                'user_name' => $reservation->user->first_name,
                'host_name' => $reservation->host_user->first_name,
                'profile_picture' => $reservation->user->profile_picture_src,
                'total' => $reservation->currency_symbol.''.$reservation->total,
                'status' => $reservation->status,
            ];
        });
        $today_reservations = Reservation::userBased()->with('room_reservations')->where('checkin',$today->format('Y-m-d'))->limit(5)->get();
        $this->view_data['today_reservations'] = $today_reservations->map(function($reservation) {
            $room_reservations = $reservation->room_reservations->groupBy('room_id')->map(function($reserve_room) {
                return [
                    'room_name' => $reserve_room->first()->hotel_room->name,
                    'guests' => $reserve_room->sum('guests').' '.Lang::get('messages.guests'),
                    'total_rooms' => $reserve_room->sum('total_rooms').' '.Lang::get('messages.rooms'),
                ];
            });
            return [
                'id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'hotel_id' => $reservation->hotel_id,
                'hotel_name' => $reservation->hotel->name,
                'location' => $reservation->hotel->hotel_address->address_line_display,
                'user_name' => $reservation->user->first_name,
                'host_name' => $reservation->host_user->first_name,
                'profile_picture' => $reservation->user->profile_picture_src,
                'total' => $reservation->currency_symbol.''.$reservation->total,
                'sub_rooms' => $room_reservations,
                'status' => $reservation->status,
            ];
        });

        return view('host.dashboard',$this->view_data);
    }

     /**
     * Get Dashboard Data for the Given Year
     *
     * @return Array
     */
    public function getBarChartData($month,$year)
    {
        $date = Carbon::createFromFormat('Y-m-d',$year.'-'.$month.'-01');
        $num_days = $date->format('t');
        $return_data['month_names'] = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $return_data['month_index'] = $date->format('m');
        $return_data['current_year'] = $date->format('Y');


        for($i=1;$i<=$num_days;$i++) {
            $date_obj = Carbon::createFromFormat('Y-m-j',$year.'-'.$month.'-'.$i);
            $labels[] = $date_obj->format('d M');
            $reservations = Reservation::userBased()->where('checkin',$date_obj->format('Y-m-d'))->where('status', 'Accepted')->get();
            $host_payout = 0;
            foreach($reservations as $reserve) {
                $host_payout += $reserve->calcHostPayoutAmount();
            }
            $earnings[] = session('currency_symbol').$host_payout;
            $count[] = $reservations->count();
            
        }        
        $return_data['data'] = array(
            'labels' => $labels,
            'earnings' => $earnings,
            'count' => $count
        );

        return $return_data;
    }

    /**
     * Get Dashboard Data for the Given Year
     *
     * @return Array
     */
    public function getDashboardData($year)
    {
        if($year == '') {
            return ['status' => false, 'status_message' => Lang::get('messages.invalid_request')];
        }

        if(date("Y") == $year) {
            $today = now()->format('Y-m-d');
            $today_users = User::whereDate('created_at',$today)->count();
            $today_hotels = Hotel::authUser()->todayOnly()->count();
            $today_reservations = Reservation::userBased()->todayOnly()->count();
        }


        $data['total_transactions'] = numberFormat(Reservation::userBased()->where('status', 'Accepted')->get()->sum('total'));
        $data['paid_out'] = numberFormat(Payout::where('status','Completed')->get()->sum('amount'));

        $reservations = Reservation::select(DB::raw('sum(total) as total,sum(service_fee) as service_fee,sum(host_fee) as host_fee'),'created_at','status','currency_code', DB::raw("DATE_FORMAT(created_at, '%Y%c') as ym"))->whereYear('created_at', '=', $year)->where('status', 'Accepted')->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y%m')"))->userBased()->get();

        $chart_array = $labels = [];
        for($month = 1; $month <= 12; $month++) {
            $dateObj = \Carbon\Carbon::createFromFormat('m',$month);
            $labels[] = $dateObj->format('M');
            $chart_array[] = $reservations->where('ym', $year.$month)->sum('total');
        }
        $data['line_chart'] = array(
            'labels' => $labels,
            'amount' => $chart_array
        );

        $data['geo_data'] = Country::join('hotel_addresses', function($join) {
                $join->on('countries.name', '=', 'hotel_addresses.country_code');
            })
            ->groupBy('hotel_addresses.country_code')
            ->select(DB::raw('count(hotel_addresses.hotel_id) as hotel_count, hotel_addresses.country_code,countries.full_name as country_name'))
            ->get();
        $penalties = UserPenalty::get();
        
        $data['currency_symbol'] = session('currency_symbol');
        
        return $data;
    }

    /**
    * Create the New Owner based on Email Signup
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function create(Request $request)
    {
        $password_rule = Password::min(8)->mixedCase()->numbers()->uncompromised();
        if(displayCrendentials()) {
            $password_rule = Password::min(8);
        }
        $rules = array(
            'email' => ['required','max:50','email','unique:users'],
            'full_name' => ['required'],
            'password' => ['required',$password_rule,'confirmed'],
            'city' => ['required'],
            'country' => ['required'],
            'phone_number' => ['required','numeric','unique:users,phone_number'],
            'manager_title' => ['required'],
        );
        $attributes = array(
            'email'           => Lang::get('messages.email'),
            'first_name'      => Lang::get('messages.first_name'),
            'last_name'       => Lang::get('messages.last_name'),
            'password'        => Lang::get('messages.password'),
            'country'         => Lang::get('messages.country'),
            'city'            => Lang::get('messages.city'),
            'phone_number'    => Lang::get('messages.mobile_number'),
            'manager_title'   => Lang::get('messages.manager_title'),
        );

        $validator = \Validator::make($request->all(), $rules, [], $attributes);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error_messages' => $validator->messages(),
            ]);
        }

        if(checkEnabled('ReCaptcha')) {
            $recaptcha_service = resolve('App\Services\ReCaptchaService');
            $captchaValidate = $recaptcha_service->validateReCaptcha($request['g-recaptcha-response']);
            if(!$captchaValidate['status']) {
                $recaptcha_error = [
                    'g-recaptcha-response' => Lang::get('messages.errors.please_complete_captcha_to_continue')
                ];
                return response()->json([
                    'status' => 'error',
                    'error_messages' => $recaptcha_error,
                ]);
            }
        }

        $ip_data = getIpBasedData($_SERVER['REMOTE_ADDR']);
        $country = resolve('Country')->where('name',$request->country)->first();

        $full_name = explode(' ',$request->full_name);

        $user = new User();
        $user->first_name = $full_name[0] ?? $request->full_name;
        $user->last_name = $full_name[1] ?? $request->full_name;
        $user->title = $request->manager_title;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->timezone = $ip_data['timezone'];
        $user->city = $request->city;
        $user->country_code = $country->name;
        $user->phone_code = $country->phone_code;
        $user->phone_number = $request->phone_number;
        $user->telephone_number = $request->telephone_number;
        $user->user_currency = $ip_data['currency_code'];
        $user->user_type = 'host';
        $user->status = 'inactive';
        $user->save();

        $user_info = $user->user_information;
        $user_info->dob = $request->dob ?? null;
        $user_info->gender = $request->gender;
        $user_info->save();

        $company = new Company;
        $company->user_id = $user->id;
        $company->company_name = $request->company_name ?? '';
        $company->company_tax_number = $request->company_tax_number ?? '';
        $company->company_tele_phone_number = $request->company_tele_phone_number ?? '';
        $company->company_fax_number = $request->company_fax_number ?? '';
        $company->address_line_1 = $request->address_line1 ?? '';
        $company->address_line_2 = $request->address_line2 ?? '';
        $company->state = $request->company_state ?? '';
        $company->country_code = $request->company_country ?? $country->name;
        $company->city = $request->company_city ?? '';
        $company->postal_code = $request->company_pincode ?? '';
        $company->company_website = $request->company_website ?? '';
        $company->company_email = $request->company_email ?? '';
        if($request->hasFile('logo')) {
            $company->deleteImageFile();

            $upload_result = $this->uploadImage($request->file('logo'),$company->getUploadPath());
            if(!$upload_result['status']) {
                $upload_error = [
                    'logo' => [Lang::get('admin_messages.failed_to_upload_image')]
                ];
                return response()->json([
                    'status' => 'error',
                    'error_messages' => $upload_error,
                ]);
            }

            $company->company_logo = $upload_result['file_name'];
            $company->upload_driver = $upload_result['upload_driver'];
        }

        $company->save();

        resolveAndSendNotification("confirmUserEmail",$user->id);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('messages.you_signup_successful'));
        
        return response()->json([
            'status' => 'redirect',
            'redirect_url' => route('host.login'),
        ]);
    }

    public function createHostValidation(Request $request)
    {
        $password_rule = Password::min(8)->mixedCase()->numbers()->uncompromised();
        if(displayCrendentials()) {
            $password_rule = Password::min(8);
        }
        $rules = array(
            'email' => ['required', 'max:50', 'email'],
            'full_name' => ['required'],
            'password' => ['required', $password_rule, 'confirmed'],
            'city' => ['required'],
            'country' => ['required'],
            'phone_number' => ['required', function ($attribute, $value, $fail) {
                if (substr($value, 0, 1) !== '0') {
                    $fail('Phone Number is invalid. It should start with "0".');
                }
            }, 'unique:users,phone_number'],
            'manager_title' => ['required','max:100'],
        );

        $attributes = array(
            'email'           => Lang::get('messages.email'),
            'full_name'      => Lang::get('messages.full_name'),
            'first_name'      => Lang::get('messages.first_name'),
            'last_name'       => Lang::get('messages.last_name'),
            'password'        => Lang::get('messages.password'),
            'country'         => Lang::get('messages.country'),
            'city'            => Lang::get('messages.city'),
            'phone_number'    => Lang::get('messages.mobile_number'),
            'manager_title'   => Lang::get('messages.manager_title'),
        );

        $user_email_count = User::authBased()->where('email',$request->email)->count();
        if($user_email_count > 0) {
            $rules['email'] =  ['required','max:50','email','unique:users'];
        }

        $validator = \Validator::make($request->all(), $rules, [], $attributes);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error_messages' => $validator->messages(),
            ]);
        }

        return response()->json([
            'status' => true,
            'status_message' => true,
        ]);
    }

    public function editCompany()
    {
        $user_id = getCurrentUserId();
        if (getCurrentUser()->user_type == 'sub_host') {
            $user_id = getCurrentUser()->host_id;
        }
        $this->view_data['main_title']  = Lang::get('admin_messages.edit_company');
        $this->view_data['active_menu'] = 'edit_company';
        $this->view_data['result'] = Company::where('user_id',$user_id)->first();
        $countries = resolve('Country');
        $this->view_data['countries'] = $countries->where('city_count','>',0)->map(function($country) {
            return [
                'name' => $country->name,
                'value' => $country->full_name.' (+'.$country->phone_code.')',
            ];
        })->pluck('value','name');
        return view('host.edit_company',$this->view_data);
    }

    public function updateCompany(Request $request)
    {
        /*$rules = [
            'company_name' => 'required',
            'company_tax_number' => 'required',
            'company_tele_phone_number' => 'required',
            'company_fax_number' => 'required',
            'address_line1' => 'required',
            'address_line2' => 'required',
            'city' => 'required',
            'company_state' => 'required',
            'country_code' => 'required',
            'company_pincode' => 'required',
            'company_website' => 'required',
            'company_email' => 'required|email',
            'logo' => 'nullable|mimes:'.view()->shared('valid_mimes'),
        ];

        $attributes = [
            'company_name' => Lang::get('messages.company_name'),
            'company_tax_number' => Lang::get('messages.company_tax_number'),
            'company_tele_phone_number' => Lang::get('messages.company_tele_phone_number'),
            'company_fax_number' => Lang::get('messages.company_fax_number'),
            'address_line1' => Lang::get('messages.address_line1'),
            'address_line2' => Lang::get('messages.ward'),
            'company_state' => Lang::get('messages.state'),
            'country_code' => Lang::get('messages.country'),
            'city' => Lang::get('messages.city_desc'),
            'company_pincode' => Lang::get('messages.pincode'),
            'company_website' => Lang::get('messages.website'),
            'company_email' => Lang::get('messages.email'),
            'logo' => Lang::get('messages.logo'),
        ];

        $this->validate($request,$rules,[],$attributes);*/

        $company = Company::findOrFail($request->id);
        if((($company->company_name == null)||($company->company_name==''))&&(isset($request->company_name)))
        {
            $company->company_name = $request->company_name;
        }
        
        if((($company->company_tax_number == null)||($company->company_tax_number==''))&&(isset($request->company_tax_number)))
        {
            $company->company_tax_number = $request->company_tax_number;
        }
         if((($company->company_tele_phone_number == null)||($company->company_tele_phone_number==''))&&(isset($request->company_tele_phone_number)))
        {
            $company->company_tele_phone_number = $request->company_tele_phone_number;
        }
        if((($company->company_fax_number == null)||($company->company_fax_number==''))&&(isset($request->company_fax_number)))
        {
            $company->company_fax_number = $request->company_fax_number;
        }
        if((($company->address_line_1 == null)||($company->address_line_1==''))&&(isset($request->address_line_1)))
        {
            $company->address_line_1 = $request->address_line_1;
        }
        if((($company->address_line_2 == null)||($company->address_line_2==''))&&(isset($request->address_line_2)))
        {
            $company->address_line_2 = $request->address_line_2;
        }
        if((($company->city == null)||($company->city==''))&&(isset($request->company_city)))
        {
            $company->city = $request->company_city;
        }
        if((($company->state == null)||($company->state==''))&&(isset($request->state)))
        {
            $company->state = $request->state;
        }
        if((($company->country_code == null)||($company->country_code==''))&&(isset($request->country_code)))
        {
            $company->country_code = $request->country_code;
        }
        if((($company->postal_code == null)||($company->postal_code==''))&&(isset($request->postal_code)))
        {
            $company->postal_code = $request->postal_code;
        }
        if((($company->company_website == null)||($company->company_website==''))&&(isset($request->company_website)))
        {
            $company->company_website = $request->company_website;
        }
        if((($company->company_email == null)||($company->company_email==''))&&(isset($request->company_email)))
        {
            $company->company_email = $request->company_email;
        }
        $company->save();

        if($request->hasFile('logo')) {
            $company->deleteImageFile();

            $upload_result = $this->uploadImage($request->file('logo'),$company->getUploadPath());
            if(!$upload_result['status']) {
                flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
                return back();
            }

            $company->company_logo = $upload_result['file_name'];
            $company->upload_driver = $upload_result['upload_driver'];
        }

        $company->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.details_updated'));
        
        return redirect()->route('host.edit_company');
    }

    /**
     * Upload Given Image to Server
     *
     * @return Array Upload Result
     */
    protected function uploadImage($image,$target_dir)
    {
        $image_handler = resolve('App\Contracts\ImageHandleInterface');
        $image_data = array();
        $image_data['name_prefix'] = 'user_';
        $image_data['add_time'] = true;
        $image_data['target_dir'] = $target_dir;

        return $image_handler->upload($image,$image_data);
    }

    /**
     * Display a hotel of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        $data['sliders'] = LoginSlider::activeOnly()->ordered()->get()->pluck('image_src');
        return view('host.login',$data);
    }

    /**
     * Authenticate Host user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $rules = array(
            'email' => 'required',
            'password' => 'required',
        );

        $attributes = array(
            'email' => Lang::get('messages.fields.username'),
            'password' => Lang::get('messages.fields.password'),
        );

        $this->validate($request,$rules,[],$attributes);
        $remember = ($request->remember_me == 'on');
        $user_data = $request->only('email','password');
        $user_data['user_type'] = function($query) {
            $query->authBased();
        };

        if (Auth::guard('host')->attempt($user_data,$remember)) {
            $user = User::where('email', $request->email)->first();
            $intented_url = session('url.intended');
            $has_host_url = \Str::contains($intented_url,global_settings('host_url'));
            if($intented_url != '' && $has_host_url) {
                return redirect($intented_url);
            }
            
            return redirect()->route('host.dashboard');
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed_to_login'),Lang::get('admin_messages.invalid_credentials'));
        }

        return redirect()->route('host.login');
    }

    /**
     * Log out current admin user.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        session()->forget('url.intended');
        Auth::guard('host')->logout();

        return redirect()->route('host.login');
    }

    /**
    * Reset Password of the owner
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function resetPassword(Request $request)
    {
        if($request->isMethod("POST")) {
            $user = User::where('email',$request->email)->authBased()->first();
            if($user == '') {
                flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.user_not_exists'));
                $redirect_url = route('host.login');
                return redirect($redirect_url);
            }
            
            resolveAndSendNotification("resetHostPassword",$user->id);
            flashMessage('success', Lang::get('messages.success'), Lang::get('messages.reset_link_sent_to_mail'));
            $redirect_url = route('host.login');
            return redirect($redirect_url);
        }
        $broker = app('auth.password.broker');
        $host = User::where('email',$request->email)->authBased()->first();
        if($host == '') {
            flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.user_not_exists'));
            $redirect_url = route('host.login');
            return redirect($redirect_url);
        }
        if($broker->tokenExists($host,$request->token)) {
            $data['email'] = $request->email;
            $data['reset_token'] = $request->token;
            $data['sliders'] = LoginSlider::activeOnly()->ordered()->get()->pluck('image_src');
            return view('host.set_password',$data);
        }
        
        flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.link_expired'));
        return redirect()->route('host.login');
    }

     /**
    * Update New Password to the owner
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function setNewPassword(Request $request)
    {
        $password_rule = Password::min(8)->mixedCase()->numbers()->uncompromised();
        $rules = array(
            'password' => ['required',$password_rule,'confirmed'],
        );

        $request->validate($rules);
        
        $broker = app('auth.password.broker');
        $user = User::where('email',$request->email)->authBased()->first();
        if(!$broker->tokenExists($user,$request->reset_token)) {
            flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.invalid_request'));
            return redirect()->route('host.login');
        }
        $user->password = $request->new_password;
        $user->save();
        
        $broker->deleteToken($user);

        Auth::guard('host')->LoginUsingId($user->id);

        flashMessage('success', Lang::get('messages.success'), Lang::get('messages.password_updated_successfully'));
        $redirect_url = route('host.dashboard');
        return redirect($redirect_url);
    }

    public function editProfile() 
    {
        $id = getCurrentUserId();
        $user_id = $id;
        if (getCurrentUser()->user_type == 'sub_host') {
            $user_id = getCurrentUser()->host_id;
        }
        $this->view_data['main_title']  = Lang::get('admin_messages.edit_profile');
        $this->view_data['active_menu'] = 'edit_profile';
        $this->view_data['result'] = User::findOrFail($id);

        $this->view_data['countries'] = resolve('Country')->where('city_count','>',0)->map(function($country) {
            return [
                'name' => $country->name,
                'value' => $country->full_name.' (+'.$country->phone_code.')',
            ];
        })->pluck('value','name');
        
        return view('host.edit_profile',$this->view_data);
    }

    public function updateProfile(Request $request,$id) 
    {
        $this->validateRequest($request, $id);
        $user = User::findOrFail($id);
        if($request->filled('password')) {
            $user->password = $request->password;
        }
        if((($user->telephone_number == null)||($user->telephone_number==''))&&(isset($request->telephone_number)))
        {
            $user->telephone_number = $request->telephone_number;
        }
        $user->save();

        if($request->hasFile('profile_picture')) {
            $image_handler = resolve('App\Contracts\ImageHandleInterface');
            $image_data['name_prefix'] = 'user_'.$user->id;
            $image_data['add_time'] = false;
            $image_data['target_dir'] = $user->getUploadPath();
            $image_data['image_size'] = $user->getImageSize();

            if(DELETE_STORAGE && $user->src != '' && $user->photo_source == 'site') {
                $image_data['name'] = $user->src;
                $handler = $user->getImageHandler();
                $handler->destroy($image_data);
            }

            $upload_result = $image_handler->upload($request->file('profile_picture'),$image_data);
            if(!$upload_result['status']) {
                flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
                return redirect()->route('host.edit');
            }
            $user->src = $upload_result['file_name'];
            $user->photo_source = 'site';
            $user->upload_driver = $upload_result['upload_driver'];
        }
        $user->save();
        $user_info = $user->user_information;
        if((($user->dob == null)||($user->dob==''))&&(isset($request->dob))&&($request->dob!='0000-11-30'))
        {
            $user_info->dob = $request->dob;
        }
        if((($user_info->gender == null)||($user_info->gender==''))&&(isset($request->gender)))
        {
            $user_info->gender = $request->gender;
        }
        $user_info->save();

        if($user->phone_number == '') {
            $user_verification = $user->user_verification;
            $user_verification->phone_number = 0;
            $user_verification->save();
        }
        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('host.edit');
    }

     /**
     * Check the specified resource Can be deleted or not.
     *
     * @param  Illuminate\Http\Request $request_data
     * @param  Int $id
     * @return Array
     */
    protected function validateRequest($request_data, $id = '')
    {
        $password_rule = Password::min(8)->mixedCase()->numbers()->uncompromised();
        if(displayCrendentials()) {
            $password_rule = Password::min(8);
        }
        $rules = array(
            'password' => ['required',$password_rule],
            'profile_picture' => ['mimes:'.view()->shared('valid_mimes'), 'max:8388608'],
        );

        if($id != '') {
            $rules['password'] = ['nullable',$password_rule];
        }

        $attributes = array(
            'first_name' => Lang::get('admin_messages.first_name'),
            'last_name' => Lang::get('admin_messages.last_name'),
            'title' => Lang::get('admin_messages.title'),
            'email' => Lang::get('admin_messages.email'),
            'password' => Lang::get('admin_messages.password'),
            'country_code' => Lang::get('admin_messages.country_code'),
            'phone_number' => Lang::get('admin_messages.phone_number'),
            'address_line_1' => Lang::get('admin_messages.address_line1'),
            'address_line_2' => Lang::get('admin_messages.address_line_2'),
            'dob' => Lang::get('admin_messages.dob'),
            'gender' => Lang::get('admin_messages.gender'),
            'profile_picture' => Lang::get('admin_messages.profile_picture'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
    public function deleteProfileImage()
    {
        $user = getCurrentUser();
		if(DELETE_STORAGE && $user->src != '' && $user->photo_source == 'site') {
			$user->deleteImageFile();
        }
		$user->src = '';
		$user->photo_source = 'site';
		$user->upload_driver = "Local";
		$user->save();
    }

    public function deleteCompanyImage()
    {
        $user = getCurrentUser();
		if(DELETE_STORAGE && $user->company->company_logo != '') {
			$user->company->deleteImageFile();
        }
        $user->company->company_logo='';
		$user->company->save();
    }
    
     public function deleteAgentProfileImage($id)
    {
        $user = User::findOrFail($id);
		if(DELETE_STORAGE && $user->src != '' && $user->photo_source == 'site') {
			$user->deleteImageFile();
        }
		$user->src = '';
		$user->photo_source = 'site';
		$user->upload_driver = "Local";
		$user->save();
    }
    

}
