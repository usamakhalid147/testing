<?php

/**
 * User Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Host
 * @category    UserController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Host;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Controller;
use App\DataTables\Host\UsersDataTable;
use App\Models\User;
use Lang;
use Auth;

class UserController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_agents');
        $this->view_data['sub_title'] = Lang::get('admin_messages.agents');
        $this->view_data['active_menu'] = 'host_users';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->setUserType('sub_host')->render('host.users.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_agent');
        $this->view_data['roles'] = \App\Models\Role::where('user_type','host')->where('user_id',getCurrentUserId())->pluck('display_name','id');
        $this->view_data['result'] = new User;
        $this->view_data['countries'] = resolve("Country")->where('city_count','>',0)->map(function($country) {
            return [
                'name' => $country->name,
                'value' => $country->full_name.' (+'.$country->phone_code.')',
            ];
        })->pluck('value','name');

        return view('host.users.add', $this->view_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        if(!isAbove18($request['dob'],'Y-n-j')) {
            $dob_error = [
                'dob' => Lang::get('admin_messages.age_should_be_above_18'),
            ];
            return back()->withErrors($dob_error);
        }

        $current_user = getCurrentUser();

        $country = resolve('Country')->where('name',$request->country_code)->first();

        $full_name = explode(' ',$request->full_name);

        $user = new User;
        $user->first_name = $full_name[0] ?? $request->full_name;
        $user->last_name = $full_name[1] ?? $request->full_name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->city = $request->city ?? '';
        $user->country_code = $country->name;
        $user->phone_code = $country->phone_code;
        $user->phone_number = $request->phone_number;
        $user->telephone_number = $request->telephone_number;
        $user->status = $request->status;
        $user->user_type = 'sub_host';
        $user->host_id = $current_user->user_type == 'sub_host' ? $current_user->host_id : $current_user->id;
        $user->role_id = $request->role_id ?? 0;
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
                return redirect()->route('admin.users');
            }

            $user->src = $upload_result['file_name'];
            $user->photo_source = 'site';
            $user->upload_driver = $upload_result['upload_driver'];
        }

        $user->save();
        
        $user_info = $user->user_information;
        $user_info->address_line_1 = $request->address_line_1;
        $user_info->address_line_2 = $request->address_line_2;
        $user_info->state = $request->state;
        $user_info->postal_code = $request->postal_code;
        $user_info->dob = $request->dob ?? '';
        $user_info->gender = $request->gender ?? '';
        $user_info->country_code = $request->country_code ?? '';
        $user_info->city = $request->city;
        $user_info->save();

        $user->attachRole($request->role_id);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
        $route = $user->user_type == 'host' ? 'host.users.host' : 'host.users';
        return redirect()->route($route);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_agent');
        $this->view_data['roles'] = \App\Models\Role::where('user_type','host')->where('user_id',getCurrentUserId())->pluck('display_name','id');

        $this->view_data['result'] = $result = User::where('user_type','sub_host')->findOrFail($id);
        if ($result->user_type == 'host') {
            $this->view_data['active_menu'] = 'host_users';
        }
        $countries = resolve("Country");
        $this->view_data['countries'] = $countries->where('city_count','>',0)->map(function($country) {
            return [
                'name' => $country->name,
                'value' => $country->full_name.' (+'.$country->phone_code.')',
            ];
        })->pluck('value','name');
        
        $transactions = \App\Models\Transaction::where('user_id',$result->host_id)->latest()->get();
        $this->view_data['user_transactions'] = $transactions->map(function($transaction) {
            return [
                'id' => $transaction->id,
                'list_type' => Lang::get('messages.'.$transaction->list_type),
                'type' => Lang::get('messages.'.$transaction->type),
                'reservation_id' => $transaction->reservation_id,
                'link' => $transaction->link,
                'link_text' => $transaction->reservation_id,
                'user_id' => $transaction->user_id,
                'user_name' => $transaction->user->first_name,
                'profile_picture' => $transaction->user->profile_picture_src,
                'amount' => $transaction->currency_symbol.''.$transaction->amount,
                'transaction_id' => $transaction->transaction_id,
                'payment_method' => Lang::get('admin_messages.'.$transaction->payment_method),
                'color' => $transaction->color,
            ];
        });

        $all_hotels = \App\Models\Hotel::where('user_id',$result->host_id)->get();
        $all_reservations = \App\Models\Reservation::where(function($query) use($result) {
            $query->where('user_id',$result->host_id)->orWhere('host_id',$result->host_id);
        })->get();

        $currency_symbol = session('currency_symbol');
        $total_earnings = $all_reservations->whereIn('status',['Accepted','Cancelled'])->where('host_id',$result->host_id)->sum(function($reservation) {
            $payout = ($reservation->host_payout != '') ? $reservation->host_payout->amount : 0;
            if ($reservation->status != 'Cancelled') {
                $payout = ($reservation->host_payout != '') ? $reservation->host_payout->amount : $reservation->calcHostPayoutAmount();
            }
            return $payout;
        });
        $total_spent = $all_reservations->where('user_id',$result->host_id)->where('status','Accepted')->sum('total');
        $admin_earnings = $all_reservations->where('code','!=','')->sum(function($reservation) use ($result) {
                if ($result->host_id == $reservation->host_id ) {
                    if ($reservation->status != 'Cancelled') {
                        return $reservation->host_fee;
                    }
                    return ($reservation->host_payout != '') ? $reservation->host_fee : 0;
                }
                if ($result->host_id == $reservation->user_id) {
                    return ($reservation->status == 'Cancelled' && $reservation->cancelled_by != 'Guest') ? 0 : $reservation->service_fee;
                }
            });

        $this->view_data['total_hotels'] = $all_hotels->count();
        $this->view_data['total_bookings'] = $all_reservations->where('user_id',$result->host_id)->where('status','Accepted')->count();
        $this->view_data['total_reservations'] = $all_reservations->where('host_id',$result->host_id)->where('status','Accepted')->count();

        $this->view_data['user_hotel_reports'] = [
            ['key' => 'listed_hotels','display_text' => Lang::get('admin_messages.listed_hotels'),"value" => $all_hotels->where('status','Listed')->count()],
            ['key' => 'unlisted_hotels','display_text' => Lang::get('admin_messages.unlisted_hotels'),"value" => $all_hotels->where('status','Unlisted')->count()],
            ['key' => 'pending_hotels','display_text' => Lang::get('admin_messages.pending_hotels'),"value" => $all_hotels->whereNotIn('status',['Listed','Unlisted'])->count()],
            ['key' => 'total_hotels','display_text' => Lang::get('admin_messages.total_hotels'),"value" => $all_hotels->count()],
            ['key' => 'accepted_reservations','display_text' => Lang::get('admin_messages.accepted_reservations'),"value" => $all_reservations->where('status','Accepted')->count()],
            ['key' => 'cancelled_reservations','display_text' => Lang::get('admin_messages.cancelled_reservations'),"value" => $all_reservations->where('status','Cancelled')->count()],
            ['key' => 'total_reservations','display_text' => Lang::get('admin_messages.total_reservations'),"value" => $all_reservations->count()],
            ['key' => 'total_earnings','display_text' => Lang::get('admin_messages.total_earnings'),"value" => $currency_symbol.''.$total_earnings],
            ['key' => 'total_spent','display_text' => Lang::get('admin_messages.total_spent'),"value" => $currency_symbol.''.$total_spent],
            ['key' => 'admin_earnings','display_text' => Lang::get('admin_messages.admin_earnings'),"value" => $currency_symbol.''.$admin_earnings],
            // ['key' => 'declined_reservations','display_text' => Lang::get('admin_messages.declined_reservations'),"value" => $all_reservations->where('status','Declined')->count()],
            // ['key' => 'pending_reservations','display_text' => Lang::get('admin_messages.pending_reservations'),"value" => $all_reservations->whereIn('status',['Pending','Pre-Accepted','Pre-Approved'])->count()],
            // ['key' => 'total_inquries','display_text' => Lang::get('admin_messages.expired_reservations'),"value" => $all_reservations->where('status','Inquiry')->count()],
        ];

        return view('host.users.edit', $this->view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateRequest($request, $id);

        if(!isAbove18($request['dob'],'Y-n-j')) {
            $dob_error = [
                'dob' => Lang::get('admin_messages.age_should_be_above_18'),
            ];
            return back()->withErrors($dob_error);
        }
        $full_name = explode(' ',$request->full_name);
        $user = User::Find($id);
        $user->first_name = $full_name[0] ?? $request->full_name;
        $user->last_name = $full_name[1] ?? $request->full_name;
        $user->email = $request->email;
        if($request->filled('password')) {
            $user->password = $request->password;
        }
        $user->country_code = $request->country_code ?? '';
        $user->city = $request->city;
        $user->phone_number = $request->phone_number;
        $user->role_id = $request->role_id ?? 0;
        $user->status = $request->status;
        $user->save();

        if($request->hasFile('profile_picture')) {
            $image_handler = resolve('App\Contracts\ImageHandleInterface');
            $image_data['name_prefix'] = 'user_'.$user->id;
            $image_data['add_time'] = false;
            $image_data['target_dir'] = $user->getUploadPath();
            $image_data['image_size'] = $user->getImageSize();

            if(DELETE_STORAGE && $user->src != '' && $user->photo_source == 'Site') {
                $image_data['name'] = $user->src;
                $handler = $user->getImageHandler();
                $handler->destroy($image_data);
            }

            $upload_result = $image_handler->upload($request->file('profile_picture'),$image_data);
            if(!$upload_result['status']) {
                flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
                return redirect()->route('host.users');
            }
            $user->src = $upload_result['file_name'];
            $user->photo_source = 'Site';
            $user->upload_driver = $upload_result['upload_driver'];
        }
        $user->verification_status = $request->verification_status;
        if($user->verification_status == 'resubmit') {
            $user->resubmit_reason = $request->resubmit_reason;
        }
        $user->save();

        $user_info = $user->user_information;
        $user_info->dob = $request->dob;
        $user_info->gender = $request->gender;
        $user_info->country_code = $request->country_code ?? '';
        $user_info->city = $request->city;
        $user_info->save();

        if($user->phone_number == '') {
            $user_verification = $user->user_verification;
            $user_verification->phone_number = 0;
            $user_verification->save();
        }
        
        $user->detachRoles();
        $user->attachRole($request->role_id);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        $route = $user->user_type == 'host' ? 'host.users.host' : 'host.users';
        return redirect()->route($route);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $can_destroy = $this->canDestroy($id);
        
        if(!$can_destroy['status']) {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy['status_message']);
            return redirect()->route('host.users');
        }
        
        try {
            User::find($id)->delete();
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        catch (\Exception $e) {
            flashMessage('danger',Lang::get('admin_messages.failed'),$e->getMessage());            
        }
        return redirect()->route('host.users');
    }

    /**
     * Login as User
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function login($id)
    {        
        if(\Auth::check()) {
            \Auth::logout();
        }
        
        if(\Auth::loginUsingId($id,true)) {
            return redirect()->route('host.dashboard');
        }

        flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.Invalid_request'));
        return redirect()->route('host.users');
    }

    /**
     * Check the specified resource Can be deleted or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy($id)
    {
        return ['status' => true,'status_message' => Lang::get('admin_messages.success')];
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
            'full_name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email,'.$id.',id'],
            'password' => ['required', $password_rule],
            'country_code' => ['nullable', 'exists:countries,name'],
            'phone_number' => ['required', function ($attribute, $value, $fail) {
                if (substr($value, 0, 1) !== '0') {
                    $fail('Phone Number is invalid. It should start with "0".');
                }
            }, 'unique:users,phone_number,'.$id],
            'city' => ['required'],
            'country_code' => ['required'],
            'profile_picture' => ['mimes:'.view()->shared('valid_mimes')],
            'status' => ['required'],
            'role_id' => ['required']
        );


        if($id != '') {
            $rules['password'] = ['nullable',$password_rule];
        }

        $attributes = array(
            'first_name' => Lang::get('admin_messages.first_name'),
            'last_name' => Lang::get('admin_messages.last_name'),
            'email' => Lang::get('admin_messages.email'),
            'password' => Lang::get('admin_messages.password'),
            'country_code' => Lang::get('admin_messages.country_code'),
            'phone_number' => Lang::get('admin_messages.phone_number'),
            'city' => Lang::get('admin_messages.city'),
            'dob' => Lang::get('admin_messages.dob'),
            'gender' => Lang::get('admin_messages.gender'),
            'profile_picture' => Lang::get('admin_messages.profile_picture'),
            'status' => Lang::get('admin_messages.status'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
}
