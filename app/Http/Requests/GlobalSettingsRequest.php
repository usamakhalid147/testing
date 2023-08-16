<?php

/**
 * Global Settings validation
 *
 * @package     HyraHotel
 * @subpackage  Requests
 * @category    GlobalSettingsRequest
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lang;

class GlobalSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->guard('admin')->user()->can('update-global_settings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'site_name' => 'required|max:50',
            'version' => 'required',
            'app_version' => 'required',
            'force_update' => 'required',
            'admin_url' => 'required',
            'play_store' => 'nullable|url',
            'app_store' => 'nullable|url',
            'is_locale_based' => 'required',
            'maintenance_mode' => 'required',
            'app_maintenance_mode' => 'required',
            'upload_driver' => 'required',
            'support_number' => 'required',
            'support_email' => 'required',
            'default_currency' => 'required|exists:currencies,code',
            'min_price' => 'required',
            'max_price' => 'required',
            'default_language'=> 'required',
            'date_format' => 'required',
            'user_inactive_days' => 'required',
            'timezone' => 'required',
            'copyright_link' => 'url',
            'copyright_text' => 'required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'site_name' => Lang::get('admin_messages.site_name'),
            'version' => Lang::get('admin_messages.version'),
            'admin_url' => Lang::get('admin_messages.admin_url'),
            'is_locale_based' => Lang::get('admin_messages.is_locale_based'),
            'maintenance_mode' => Lang::get('admin_messages.maintenance_mode'),
            'app_maintenance_mode' => Lang::get('admin_messages.app_maintenance_mode'),
            'upload_driver' => Lang::get('admin_messages.upload_driver'),
            'support_number' => Lang::get('admin_messages.support_number'),
            'support_email' => Lang::get('admin_messages.support_email'),
            'date_format' => Lang::get('admin_messages.date_format'),
            'timezone' => Lang::get('admin_messages.timezone'),
            'copyright_link' => Lang::get('admin_messages.copyright_link'),
            'copyright_text' => Lang::get('admin_messages.copyright_text'),
        ];
    }
}
