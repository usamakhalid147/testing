<?php

/**
 * Referral Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers
 * @category    ReferralController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ReferralUser;
use Auth;
use Lang;
use Validator;

class ReferralController extends Controller
{
    /**
     * Display Referral Home page
     *
     * @return \Illuminate\Http\Response
     */
    public function invite()
    {
        $currency_symbol = session('currency_symbol');
        $data['new_referral_credit'] = $currency_symbol.referral_settings('new_referral_credit');
        $data['user_become_guest_credit'] = $currency_symbol.referral_settings('user_become_guest_credit');
        $data['referral_amount'] = $currency_symbol.round(referral_settings('user_become_guest_credit'));
        
        if(Auth::check()) {
            $user = Auth::user();
            $referral_users = ReferralUser::authUser()->get();
            $pending_credit = $referral_users->where('user_id',Auth::id())->where('user_become_guest_status',0)->sum('user_become_guest_amount');
            $available_credit = $referral_users->where('user_id',Auth::id())->sum('user_credited_amount') + $referral_users->where('referral_user_id',Auth::id())->sum('referral_credited_amount');
            $data['referral_link'] = route('invite_referral',['username' => $user->username]);
            $data['pending_credit'] = $currency_symbol.''.numberFormat($pending_credit);
            $data['available_credit'] = $currency_symbol.''.numberFormat($available_credit);
        }
        
        return view('referral.invite',$data);
    }

    /**
     * Display Referral Page
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function inviteReferral(Request $request)
    {
        if(Auth::check()) {
            $redirect_url = resolveRoute('invite');
            return redirect($redirect_url);
        }

        $username = $request->username;
        $user = User::where('username',$username)->first();
        if($user == '') {
            $redirect_url = resolveRoute('home');
            return redirect($redirect_url);
        }

        $data['user'] = $user;
        session(['referral_username' => $username]);
        $currency_symbol = session('currency_symbol');
        $data['referral_amount'] = $currency_symbol.round(referral_settings('user_become_guest_credit'));
        $data['new_referral_credit'] = $currency_symbol.referral_settings('new_referral_credit');
        $data['user_become_guest_credit'] = $currency_symbol.referral_settings('user_become_guest_credit');

        return view('referral.referral',$data);
    }

    /**
     * Display Referral Page
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getReferrals(Request $request)
    {
        $currency_symbol = session('currency_symbol');
        $pending_recruited_users = ReferralUser::where('user_become_guest_status',0)->where('user_id',Auth::id())->get();
        $pending_recruited_users = $pending_recruited_users->map(function ($pending_recruited_user) use ($currency_symbol) {
            $user = User::findOrFail($pending_recruited_user->referral_user_id);
            return [
                'username' => $user->first_name." ".$user->last_name,
                'profile_picture_src' => $user->profile_picture_src,
                'user_become_guest_amount' => $currency_symbol.''.numberFormat($pending_recruited_user->user_become_guest_amount),
            ];
        });

        $recruited_users = ReferralUser::where('user_become_guest_status',1)->where('user_id',Auth::id())->get();
        $available_credit = ReferralUser::where('user_id',Auth::id())->sum('user_credited_amount') + ReferralUser::where('referral_user_id',Auth::id())->sum('referral_credited_amount');
        $available_credit = $currency_symbol.''.numberFormat($available_credit);
        $recruited_users = $recruited_users->map(function ($recruited_user) use ($currency_symbol) {
            $user = User::findOrFail($recruited_user->referral_user_id);
            return [
                'username' => $user->first_name." ".$user->last_name,
                'profile_picture_src' => $user->profile_picture_src,
                'total_earnings' => $currency_symbol.''.numberFormat($recruited_user->referral_credited_amount),
            ];
        });

        return response()->json([
            'pending_recruited_users' => $pending_recruited_users,
            'recruited_users' => $recruited_users,
            'available_credit' => $available_credit,
        ]);
    }

    /**
     * Invite Users
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function inviteUser(Request $request)
    {
        $rules = [
            'email' => 'required|email',
        ];
        $validate = Validator::make($request->all(),$rules,[],[]);
        if($validate->fails()) {
            return response()->json([
                'status' => false,
                'error' => true,
                'error_message' => $validate->messages(),
            ]);
        }

        $result = resolveAndSendNotification("inviteGuest",$request->email);
        if($result['status']) {
            $status = true;
            $status_message = Lang::get('messages.mail_sent_successfully');
        }
        else {
            $status = false;
            $status_message = Lang::get('messages.currently_busy');   
        }
        return response()->json([
            'status' => $status,
            'error' => false,
            'status_message' => $status_message,
            'error_message' => Lang::get('messages.failed_to_send_mail'),
        ]);
    }
}
