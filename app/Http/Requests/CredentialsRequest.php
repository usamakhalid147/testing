<?php

/**
 * API and Payment Gateway Credential validation
 *
 * @package     HyraHotel
 * @subpackage  Requests
 * @category    CredentialsRequest
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lang;

class CredentialsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $permission = ($this->active_menu == 'api_credentials') ? 'api_credentials' : 'payment_gateways';
        return auth()->guard('admin')->user()->can('update-'.$permission);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->active_menu == 'api_credentials') {
            $rules = [
                'map_api_key' => 'required',
                'map_server_key' => 'required',
                'google_client_id' => 'required',
                'google_secret_key' => 'required',
                'facebook_app_id' => 'required',
                'facebook_app_secret' => 'required',
                'account_sid' => 'required',
                'auth_token' => 'required',
                'from_number' => 'required',
                'apple_service_id' => 'required',
                'apple_team_id' => 'required',
                'apple_key_id' => 'required',
                'apple_key_file' => 'valid_extensions:p8',
            ];

            $cloudinary_rules = [
                'cloud_name' => 'required',
                'cloud_api_key' => 'required',
                'cloud_api_secret' => 'required',
            ];
            $rules = array_merge($rules,$cloudinary_rules);

            $recaptcha_rules = [
                'recaptcha_site_key' => 'required',
                'recaptcha_secret_key' => 'required',
            ];
            $rules = array_merge($rules,$recaptcha_rules);

            $firebase_rules = [
                'firebase_api_key' => 'required',
                'firebase_auth_domain' => 'required',
                'firebase_database_url' => 'required',
                'firebase_project_id' => 'required',
                'firebase_storage_bucket' => 'required',
                'firebase_messaging_sender_id' => 'required',
                'firebase_app_id' => 'required',
                'firebase_service_account' => 'valid_extensions:json',
            ];
            $rules = array_merge($rules,$firebase_rules);
        }
        else {
            $rules = [
                'stripe_publish_key' => 'required',
                'stripe_secret_key' => 'required',
                'stripe_currency_code' => 'required',
                'paypal_mode' => 'required',
                'paypal_client_id' => 'required',
                'paypal_secret_key' => 'required',
                'paypal_currency_code' => 'required',
            ];
        }
        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'map_api_key'           => Lang::get('admin_messages.map_api_key'),
            'map_server_key'        => Lang::get('admin_messages.map_server_key'),
            'google_client_id'      => Lang::get('admin_messages.client_id'),
            'google_secret_key'     => Lang::get('admin_messages.secret_key'),
            'facebook_app_id'       => Lang::get('admin_messages.app_id'),
            'facebook_app_secret'   => Lang::get('admin_messages.app_secret'),
            'stripe_publish_key'    => Lang::get('admin_messages.publish_key'),
            'stripe_secret_key'     => Lang::get('admin_messages.secret_key'),
            'paypal_mode'           => Lang::get('admin_messages.mode'),
            'paypal_username'       => Lang::get('admin_messages.username'),
            'paypal_password'       => Lang::get('admin_messages.password'),
            'paypal_signature'      => Lang::get('admin_messages.signature'),
            'paypal_client_id'      => Lang::get('admin_messages.client_id'),
            'paypal_secret_key'     => Lang::get('admin_messages.secret_key'),
            'stripe_currency_code'  => Lang::get('admin_messages.payment_currency'),
            'paypal_currency_code'  => Lang::get('admin_messages.payment_currency'),
            'cloud_name'            => Lang::get('admin_messages.cloud_name'),
            'cloud_api_key'         => Lang::get('admin_messages.cloud_api_key'),
            'cloud_api_secret'      => Lang::get('admin_messages.cloud_api_secret'),
            'account_sid'           => Lang::get('admin_messages.account_sid'),
            'auth_token'            => Lang::get('admin_messages.auth_token'),
            'from_number'           => Lang::get('admin_messages.from_number'),
            'recaptcha_site_key'    => Lang::get('admin_messages.site_key'),
            'recaptcha_secret_key'  => Lang::get('admin_messages.secret_key'),
            'firebase_api_key'      => Lang::get('admin_messages.api_key'),
            'firebase_auth_domain'  => Lang::get('admin_messages.auth_domain'),
            'firebase_database_url' => Lang::get('admin_messages.database_url'),
            'firebase_project_id'   => Lang::get('admin_messages.project_id'),
            'firebase_storage_bucket' => Lang::get('admin_messages.storage_bucket'),
            'firebase_messaging_sender_id' => Lang::get('admin_messages.messaging_sender_id'),
            'firebase_app_id'       => Lang::get('admin_messages.app_id'),
            'firebase_service_account' => Lang::get('admin_messages.service_account'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'apple_key_file.valid_extensions' => \Lang::get('validation.mimes',['values'=>'p8']),
            'firebase_service_account.valid_extensions' => \Lang::get('validation.mimes',['values'=>'json']),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if(!file_exists(resource_path('apple/'.credentials('key_file','Apple'))) && $this->file('apple_key_file') == '') {
                $validator->errors()->add('apple_key_file', \Lang::get('validation.required',['attribute'=> \Lang::get('admin_messages.key_file')]));
            }
        });
    }
}