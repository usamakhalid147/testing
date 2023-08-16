<?php

/**
 * Credential Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    CredentialController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CredentialsRequest;
use App\Models\Currency;
use App\Models\Credential;
use Lang;

class CredentialController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        if(request()->route() && request()->route()->getName() == 'admin.payment_gateways') {
            $this->view_data['active_menu'] = $active_menu = 'payment_gateways';
        }
        else {
            $this->view_data['active_menu'] = $active_menu = 'api_credentials';
        }

        $this->view_data['main_title'] = Lang::get('admin_messages.manage_'.$active_menu);
        $this->view_data['sub_title'] = Lang::get('admin_messages.'.$active_menu);
        $this->view_data['update_url'] = route('admin.'.$active_menu.'.update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->view_data['payment_currencies'] = Currency::activeOnly()->get()->pluck('code','code');
        return view('admin.credentials.edit', $this->view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\CredentialsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(CredentialsRequest $request)
    {
        Credential::where(['name' => 'is_enabled', 'site' => 'GoogleMap'])->update(['value' => $request->is_google_map_enabled]);
        Credential::where(['name' => 'map_api_key', 'site' => 'GoogleMap'])->update(['value' => $request->map_api_key]);
        Credential::where(['name' => 'map_server_key', 'site' => 'GoogleMap'])->update(['value' => $request->map_server_key]);

        Credential::where(['name' => 'is_enabled', 'site' => 'Google'])->update(['value' => $request->is_google_enabled]);
        Credential::where(['name' => 'client_id', 'site' => 'Google'])->update(['value' => $request->google_client_id]);
        Credential::where(['name' => 'secret_key', 'site' => 'Google'])->update(['value' => $request->google_secret_key]);

        Credential::where(['name' => 'is_enabled', 'site' => 'Facebook'])->update(['value' => $request->is_facebook_enabled]);
        Credential::where(['name' => 'app_id', 'site' => 'Facebook'])->update(['value' => $request->facebook_app_id]);
        Credential::where(['name' => 'app_secret', 'site' => 'Facebook'])->update(['value' => $request->facebook_app_secret]);

        Credential::where(['name' => 'cloud_name', 'site' => 'Cloudinary'])->update(['value' => $request->cloud_name]);
        Credential::where(['name' => 'api_key', 'site' => 'Cloudinary'])->update(['value' => $request->cloud_api_key]);
        Credential::where(['name' => 'api_secret', 'site' => 'Cloudinary'])->update(['value' => $request->cloud_api_secret]);

        Credential::where(['name' => 'is_enabled', 'site' => 'Twilio'])->update(['value' => $request->is_twilio_enabled]);
        Credential::where(['name' => 'account_sid', 'site' => 'Twilio'])->update(['value' => $request->account_sid]);
        Credential::where(['name' => 'auth_token', 'site' => 'Twilio'])->update(['value' => $request->auth_token]);
        Credential::where(['name' => 'from_number', 'site' => 'Twilio'])->update(['value' => $request->from_number]);

        Credential::where(['name' => 'is_enabled', 'site' => 'ReCaptcha'])->update(['value' => $request->is_recaptcha_enabled]);
        Credential::where(['name' => 'version', 'site' => 'ReCaptcha'])->update(['value' => $request->recaptcha_version]);
        Credential::where(['name' => 'site_key', 'site' => 'ReCaptcha'])->update(['value' => $request->recaptcha_site_key]);
        Credential::where(['name' => 'secret_key', 'site' => 'ReCaptcha'])->update(['value' => $request->recaptcha_secret_key]);

        Credential::where(['name' => 'is_enabled', 'site' => 'Firebase'])->update(['value' => $request->is_firebase_enabled]);
        Credential::where(['name' => 'api_key','site' => 'Firebase'])->update(['value' => $request->firebase_api_key]);
        Credential::where(['name' => 'auth_domain','site' => 'Firebase'])->update(['value' => $request->firebase_auth_domain]);
        Credential::where(['name' => 'database_url','site' => 'Firebase'])->update(['value' => $request->firebase_database_url]);
        Credential::where(['name' => 'project_id','site' => 'Firebase'])->update(['value' => $request->firebase_project_id]);
        Credential::where(['name' => 'storage_bucket','site' => 'Firebase'])->update(['value' => $request->firebase_storage_bucket]);
        Credential::where(['name' => 'messaging_sender_id','site' => 'Firebase'])->update(['value' => $request->firebase_messaging_sender_id]);
        Credential::where(['name' => 'app_id','site' => 'Firebase'])->update(['value' => $request->firebase_app_id]);

        Credential::where(['name' => 'is_enabled', 'site' => 'Conveythis'])->update(['value' => $request->is_conveythis_enabled]);
        if ($request->hasFile('firebase_service_account')) {
            $service_account = $request->file('firebase_service_account');
            $upload_handler = resolve("App\Services\ImageHandlers\LocalImageHandler");

            $file_data['name_prefix'] = 'service_account_';
            $file_data['add_time'] = true;
            $file_data['target_path'] = resource_path().'/firebase/';

            $upload_result = $upload_handler->upload($request->file('firebase_service_account'),$file_data);
            if(!$upload_result['status']) {
                flashMessage('danger',$upload_result['status_message'],Lang::get('admin_messages.failed'));
                return back()->withInput();
            }

            Credential::where(['name' => 'service_account','site' => 'Firebase'])->update(['value' => $upload_result['file_name']]);
        }

        Credential::where(['name' => 'is_enabled', 'site' => 'Apple'])->update(['value' => $request->is_apple_enabled]);
        Credential::where(['name' => 'service_id','site' => 'Apple'])->update(['value' => $request->apple_service_id]);
        Credential::where(['name' => 'team_id','site' => 'Apple'])->update(['value' => $request->apple_team_id]);
        Credential::where(['name' => 'key_id','site' => 'Apple'])->update(['value' => $request->apple_key_id]);

        if ($request->hasFile('apple_key_file')) {
            $apple_key_file = $request->file('apple_key_file');
            $upload_handler = resolve("App\Services\ImageHandlers\LocalImageHandler");

            $file_data['name_prefix'] = 'key_';
            $file_data['add_time'] = true;
            $file_data['ext'] = 'txt';
            $file_data['target_path'] = resource_path().'/apple/';

            $upload_result = $upload_handler->upload($apple_key_file,$file_data);
            if(!$upload_result['status']) {
                flashMessage('danger',Lang::get('admin_messages.failed'),$upload_result['status_message']);
                return back()->withInput();
            }

            Credential::where(['name' => 'key_file','site' => 'Apple'])->update(['value' => $upload_result['file_name']]);
        }

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.api_credentials',['current_tab' => $request->current_tab]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\CredentialsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function paymentUpdate(CredentialsRequest $request)
    {
        // Update Stripe Details
        Credential::where(['name' => 'is_enabled', 'site' => 'Stripe'])->update(['value' => $request->is_stripe_enabled]);
        Credential::where(['name' => 'publish_key', 'site' => 'Stripe'])->update(['value' => $request->stripe_publish_key]);
        Credential::where(['name' => 'secret_key', 'site' => 'Stripe'])->update(['value' => $request->stripe_secret_key]);
        Credential::where(['name' => 'payment_currency', 'site' => 'Stripe'])->update(['value' => $request->stripe_currency_code]);
        Credential::where(['name' => 'account_type', 'site' => 'Stripe'])->update(['value' => $request->stripe_account_type]);

        // Update Paypal Details
        Credential::where(['name' => 'is_enabled', 'site' => 'Paypal'])->update(['value' => $request->is_paypal_enabled]);
        Credential::where(['name' => 'paymode', 'site' => 'Paypal'])->update(['value' => $request->paypal_mode]);
        Credential::where(['name' => 'client_id', 'site' => 'Paypal'])->update(['value' => $request->paypal_client_id]);
        Credential::where(['name' => 'secret_key', 'site' => 'Paypal'])->update(['value' => $request->paypal_secret_key]);
        Credential::where(['name' => 'payment_currency', 'site' => 'Paypal'])->update(['value' => $request->paypal_currency_code]);
        
        // Update OnePay Details
        Credential::where(['name' => 'is_enabled', 'site' => 'OnePay'])->update(['value' => $request->is_one_pay_enabled]);
        Credential::where(['name' => 'paymode', 'site' => 'OnePay'])->update(['value' => $request->one_pay_mode]);
        Credential::where(['name' => 'access_code', 'site' => 'OnePay'])->update(['value' => $request->one_pay_access_code]);
        Credential::where(['name' => 'merchant', 'site' => 'OnePay'])->update(['value' => $request->one_pay_merchant]);
        Credential::where(['name' => 'hash_key', 'site' => 'OnePay'])->update(['value' => $request->one_pay_hash_key]);
        Credential::where(['name' => 'payment_currency', 'site' => 'OnePay'])->update(['value' => $request->one_pay_currency_code]);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.payment_gateways',['current_tab' => $request->current_tab]);
    }
}