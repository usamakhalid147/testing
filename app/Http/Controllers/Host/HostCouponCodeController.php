<?php

/**
 * Host Coupon Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Host
 * @category    HostCouponCodeController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Host;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\Host\HostCouponCodesDataTable;
use App\Models\HostCouponCode;
use Lang;
use Auth;

class HostCouponCodeController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title']  = Lang::get('admin_messages.manage_coupon_code');
        $this->view_data['sub_title'] = Lang::get('admin_messages.coupon_codes');
        $this->view_data['active_menu'] = 'coupon_codes';
    }

    /**
     * Display a hotel of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HostCouponCodesDataTable $dataTable)
    {
        return $dataTable->render('host.coupon_codes.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_coupon_code');
        $this->view_data['result'] = new HostCouponCode;
        $this->view_data['currencies'] = resolve('Currency')->activeOnly()->pluck('code','code');
        return view('host.coupon_codes.add', $this->view_data);
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

        $coupon_code = new HostCouponCode;
        $coupon_code->user_id = getHostId();
        $coupon_code->type = $request->type;
        $coupon_code->code = $request->code;
        $coupon_code->value = $request->value;
        $coupon_code->currency_code = session('currency');
        $coupon_code->min_amount = $request->min_amount;
        $coupon_code->per_user_limit = $request->per_user_limit;
        $coupon_code->per_list_limit = $request->per_list_limit;
        $coupon_code->start_date = $request->start_date;
        $coupon_code->end_date = $request->end_date;
        $coupon_code->visible_on_public = $request->visible_on_public;
        $coupon_code->status = $request->status;
        $coupon_code->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));

        $redirect_url = route('host.coupon_codes');
        return redirect($redirect_url);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_coupon_code');
        $this->view_data['result'] = HostCouponCode::authUser()->findOrFail($id);
        $this->view_data['currencies'] = resolve('Currency')->activeOnly()->pluck('code','code');
        return view('host.coupon_codes.edit', $this->view_data);
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
        $coupon_code = HostCouponCode::authUser()->Find($id);
        $coupon_code->user_id = getHostId();
        $coupon_code->type = $request->type;
        $coupon_code->code = $request->code;
        $coupon_code->value = $request->value;
        $coupon_code->currency_code = session('currency');
        $coupon_code->min_amount = $request->min_amount;
        $coupon_code->per_user_limit = $request->per_user_limit;
        $coupon_code->per_list_limit = $request->per_list_limit;
        $coupon_code->start_date = $request->start_date;
        $coupon_code->end_date = $request->end_date;
        $coupon_code->visible_on_public = $request->visible_on_public;
        $coupon_code->status = $request->status;
        $coupon_code->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        $redirect_url = route('host.coupon_codes');
        return redirect($redirect_url);
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
        
        if($can_destroy['status']) {
            HostCouponCode::find($id)->delete();
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy['status_message']);
        }
        $redirect_url = route('host.coupon_codes');
        return redirect($redirect_url);
    }

    /**
     * Check the specified resource Can be deleted or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy($id)
    {
        return ['status' => true,'status_message' => ''];
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
            'type' => 'required|in:amount,percentage',
            'code' => 'required|max:20|unique:host_coupon_codes,code,'.$id.',id,user_id,'.getCurrentUserId(),
            'value' => 'required|numeric|min:1',
            'min_amount' => 'required|numeric|min:0',
            'per_user_limit' => 'required|numeric|min:1',
            'per_list_limit' => 'required|numeric|min:1',
            'visible_on_public' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
        );

        if($request_data->type == 'amount') {
            $rules['value'] .= '|lte:'.$request_data->min_amount;
        }

        $attributes = array(
            'type' => Lang::get('admin_messages.type'),
            'code' => Lang::get('admin_messages.code'),
            'value' => Lang::get('admin_messages.value'),
            'min_amount' => Lang::get('admin_messages.min_amount'),
            'per_user_limit' => Lang::get('admin_messages.per_user_limit'),
            'per_list_limit' => Lang::get('admin_messages.per_list_limit'),
            'visible_on_public' => Lang::get('admin_messages.visible_on_public'),
            'start_date' => Lang::get('admin_messages.start_date'),
            'end_date' => Lang::get('admin_messages.end_date'),
            'status' => Lang::get('admin_messages.status'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
}
