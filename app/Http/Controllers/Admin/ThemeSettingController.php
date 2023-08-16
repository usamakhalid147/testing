<?php

/**
 * Theme Setting Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    ThemeSettingController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use Validator;
use Lang;

class ThemeSettingController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_theme_settings');
        $this->view_data['active_menu'] = 'theme_settings';
        $this->view_data['sub_title'] = Lang::get('admin_messages.theme_settings');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.theme_settings.edit', $this->view_data);
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

        GlobalSetting::where(['name' => 'font_script_url'])->update(['value' => $request->font_script_url]);
        GlobalSetting::where(['name' => 'font_family'])->update(['value' => $request->font_family]);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.theme_settings');
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
            'font_script_url' => 'required|url',
            'font_family' => 'required',
        );

        $attributes = array(
            'font_script_url' => Lang::get('admin_messages.font_script_url'),
            'font_family' => Lang::get('admin_messages.font_family'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
}
