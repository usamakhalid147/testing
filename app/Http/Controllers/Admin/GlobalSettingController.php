<?php

/**
 * Global Setting Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    GlobalSettingController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GlobalSettingsRequest;
use App\Http\Requests\SiteImagesRequest;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Artisan;
use Lang;

class GlobalSettingController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title']  = Lang::get('admin_messages.manage_global_settings');
        $this->view_data['active_menu'] = 'global_settings';
        $this->view_data['sub_title'] = Lang::get('admin_messages.global_settings');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->view_data['maintenance_mode'] = (\App::isDownForMaintenance()) ? 'down' : 'up';
        $this->view_data['date_formats'] = resolve("DateFormat")->pluck('display_format','id');
        $this->view_data['timezones'] = \App\Models\Timezone::get()->pluck('name','value');
        $this->view_data['backup_period_array'] = array(
            'never' => Lang::get('admin_messages.never'),
            'daily' => Lang::get('admin_messages.daily'),
            'weekly' => Lang::get('admin_messages.weekly'),
            'twiceMonthly' => Lang::get('admin_messages.twice_month'),
            'monthly' => Lang::get('admin_messages.monthly'),
        );

        return view('admin.global_settings.edit', $this->view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GlobalSettingsRequest $request)
    {
        GlobalSetting::where(['name' => 'site_name'])->update(['value' => $request->site_name]);
        GlobalSetting::where(['name' => 'version'])->update(['value' => $request->version]);
        GlobalSetting::where(['name' => 'app_version'])->update(['value' => $request->app_version]);
        GlobalSetting::where(['name' => 'force_update'])->update(['value' => $request->force_update]);
        GlobalSetting::where(['name' => 'android_app_maintenance_mode'])->update(['value' => $request->app_maintenance_mode]);
        GlobalSetting::where(['name' => 'ios_app_maintenance_mode'])->update(['value' => $request->app_maintenance_mode]);
        GlobalSetting::where(['name' => 'admin_url'])->update(['value' => $request->admin_url]);
        GlobalSetting::where(['name' => 'host_url'])->update(['value' => $request->host_url]);
        GlobalSetting::where(['name' => 'play_store'])->update(['value' => $request->play_store]);
        GlobalSetting::where(['name' => 'app_store'])->update(['value' => $request->app_store]);
        GlobalSetting::where(['name' => 'auto_payout'])->update(['value' => $request->auto_payout]);
        GlobalSetting::where(['name' => 'referral_enabled'])->update(['value' => $request->referral_enabled]);
        GlobalSetting::where(['name' => 'host_can_add_coupon'])->update(['value' => $request->host_can_add_coupon]);
        GlobalSetting::where(['name' => 'is_locale_based'])->update(['value' => $request->is_locale_based]);
        GlobalSetting::where(['name' => 'upload_driver'])->update(['value' => $request->upload_driver]);
        GlobalSetting::where(['name' => 'support_number'])->update(['value' => $request->support_number]);
        GlobalSetting::where(['name' => 'support_email'])->update(['value' => $request->support_email]);
        GlobalSetting::where(['name' => 'default_currency'])->update(['value' => $request->default_currency]);
        GlobalSetting::where(['name' => 'default_language'])->update(['value' => $request->default_language]);
        GlobalSetting::where(['name' => 'date_format'])->update(['value' => $request->date_format]);
        GlobalSetting::where(['name' => 'timezone'])->update(['value' => $request->timezone]);
        GlobalSetting::where(['name' => 'copyright_link'])->update(['value' => $request->copyright_link]);
        GlobalSetting::where(['name' => 'copyright_text'])->update(['value' => $request->copyright_text]);
        GlobalSetting::where(['name' => 'default_user_status'])->update(['value' => $request->default_user_status]);
        GlobalSetting::where(['name' => 'default_listing_status'])->update(['value' => $request->default_listing_status]);
        GlobalSetting::where(['name' => 'backup_period'])->update(['value' => $request->backup_period]);
        GlobalSetting::where(['name' => 'min_price'])->update(['value' => $request->min_price]);
        GlobalSetting::where(['name' => 'max_price'])->update(['value' => $request->max_price]);
        
        GlobalSetting::where(['name' => 'head_code'])->update(['value' => $request->head_code]);
        GlobalSetting::where(['name' => 'foot_code'])->update(['value' => $request->foot_code]);
        GlobalSetting::where(['name' => 'user_inactive_days'])->update(['value' => $request->user_inactive_days]);

        $maintenance_mode = (\App::isDownForMaintenance()) ? 'down' : 'up';
        if($maintenance_mode != $request->maintenance_mode) {
            $args = [];
            if($request->maintenance_mode == 'down') {
                $uuid = \Str::uuid()->toString();
                GlobalSetting::where(['name' => 'maintenance_mode_secret'])->update(['value' => $uuid]);
                $args = ['--secret' => $uuid];
            }
            else {
                GlobalSetting::where(['name' => 'maintenance_mode_secret'])->update(['value' => '']);
            }
            Artisan::call($request->maintenance_mode,$args);
        }

        $image_handler = resolve('App\Contracts\ImageHandleInterface');
        $global_settings = new GlobalSetting;
        
        // Upload Primary logo
        if($request->hasFile('primary_logo')) {
            $upload_result = $this->uploadImage($request->file('primary_logo'),$global_settings->filePath,'logo');
            if($upload_result['status']) {
                GlobalSetting::where(['name' => 'logo'])->update(['value' => $upload_result['file_name']]);
                GlobalSetting::where(['name' => 'logo_driver'])->update(['value' => $upload_result['upload_driver']]);
            }
            else {
                $upload_failed = true;
            }
        }

        // Upload Secondary logo
        if($request->hasFile('secondary_logo')) {
            $upload_result = $this->uploadImage($request->file('secondary_logo'),$global_settings->filePath,'secondary_logo');
            if($upload_result['status']) {
                GlobalSetting::where(['name' => 'secondary_logo'])->update(['value' => $upload_result['file_name']]);
                GlobalSetting::where(['name' => 'secondary_logo_driver'])->update(['value' => $upload_result['upload_driver']]);
            }
            else {
                $upload_failed = true;
            }
        }

        // Upload fav icon
        if($request->hasFile('favicon')) {
            $upload_result = $this->uploadImage($request->file('favicon'),$global_settings->filePath,'favicon');
            if($upload_result['status']) {
                GlobalSetting::where(['name' => 'favicon'])->update(['value' => $upload_result['file_name']]);
                GlobalSetting::where(['name' => 'favicon_driver'])->update(['value' => $upload_result['upload_driver']]);
            }
            else {
                $upload_failed = true;
            }
        }

        if(isset($upload_failed)) {
            flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
        }
        else {
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        }

        if($request->admin_url != global_settings('admin_url')) {
            return redirect(url($request->admin_url.'/global-settings'));
        }
        return redirect()->route('admin.global_settings');
    }

    /**
     * Upload Given Image to Server
     *
     * @return Object Upload Result
     */
    protected function uploadImage($image,$target_dir,$name_prefix)
    {
        $image_handler = resolve('App\Contracts\ImageHandleInterface');

        $image_data['name_prefix'] = $name_prefix;
        $image_data['target_dir'] = $target_dir;

        return $image_handler->upload($image,$image_data);
    }
}