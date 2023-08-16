<?php

/**
 * Fee Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    FeeController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Fee;
use Lang;

class FeeController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_fees');
        $this->view_data['active_menu'] = 'fees';
        $this->view_data['sub_title'] = Lang::get('admin_messages.fees');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->view_data['result'] = resolve('Fee');
        return view('admin.fees.edit', $this->view_data);
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

        Fee::where(['name' => 'service_fee_type'])->update(['value' => $request->service_fee_type]);
        Fee::where(['name' => 'service_fee'])->update(['value' => $request->service_fee]);
        Fee::where(['name' => 'min_service_fee'])->update(['value' => $request->min_service_fee]);

        Fee::where(['name' => 'host_fee'])->update(['value' => $request->host_fee]);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.fees');
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
            'service_fee_type' => 'required|in:fixed,percentage',
            'service_fee' => 'required|numeric',
            'min_service_fee' => 'required|numeric',
            'host_fee' => 'required|numeric',
        );

        $attributes = array(
            'service_fee_type'  => Lang::get('admin_messages.service_fee_type'),
            'service_fee'       => Lang::get('admin_messages.service_fee'),
            'min_service_fee'   => Lang::get('admin_messages.min_service_fee'),
            'host_fee'       => Lang::get('admin_messages.host_fee'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
}
