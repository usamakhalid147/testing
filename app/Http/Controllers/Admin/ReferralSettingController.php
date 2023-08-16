<?php

/**
 * Referral Setting Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    ReferralSettingController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ReferralSetting;
use Validator;
use Lang;

class ReferralSettingController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_referral_settings');
        $this->view_data['active_menu'] = 'referral_settings';
        $this->view_data['sub_title'] = Lang::get('admin_messages.referral_settings');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.referral_settings.edit', $this->view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validateRequest($request);

        ReferralSetting::where(['name' => 'per_user_limit'])->update(['value' => $request->per_user_limit]);
        ReferralSetting::where(['name' => 'user_become_guest_credit'])->update(['value' => $request->user_become_guest_credit]);
        // ReferralSetting::where(['name' => 'user_become_host_credit'])->update(['value' => $request->user_become_host_credit]);
        ReferralSetting::where(['name' => 'new_referral_credit'])->update(['value' => $request->new_referral_credit]);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.referral_settings');
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
        $rules = array(
            'per_user_limit' => 'required|numeric',
            'user_become_guest_credit' => 'required|numeric',
            // 'user_become_host_credit' => 'required|numeric',
            'new_referral_credit' => 'required|numeric',
        );

        $attributes = array(
            'per_user_limit' => Lang::get('admin_messages.per_user_limit'),
            'user_become_guest_credit' => Lang::get('admin_messages.user_become_guest_credit'),
            // 'user_become_host_credit' => Lang::get('admin_messages.user_become_host_credit'),
            'new_referral_credit' => Lang::get('admin_messages.new_referral_credit'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
}
