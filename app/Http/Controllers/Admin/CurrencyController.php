<?php

/**
 * Currency Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    CurrencyController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\CurrenciesDataTable;
use App\Models\Currency;
use Lang;

class CurrencyController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_currencies');
        $this->view_data['active_menu'] = 'currencies';
        $this->view_data['sub_title'] = Lang::get('admin_messages.currencies');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CurrenciesDataTable $dataTable)
    {
        return $dataTable->render('admin.currencies.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_currency');
        $this->view_data['result'] = new currency;
        return view('admin.currencies.add', $this->view_data);
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

        $validated = $request->only(['name', 'code', 'symbol', 'rate', 'status']);

        Currency::create($validated);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
        return redirect()->route('admin.currencies');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_currency');
        $this->view_data['result'] = Currency::findOrFail($id);
        return view('admin.currencies.edit', $this->view_data);
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

        $currency = Currency::Find($id);

        $update = $this->canDestroy($currency->code);
        if(!$update['status']) {
            $code_error = [
                'code' => $update['status_message'],
            ];
            return back()->withErrors($code_error)->withInput();
        }
        
        $currency->name = $request->name;
        $currency->code = $request->code;
        $currency->symbol = $request->symbol;
        $currency->rate = $request->rate;
        $currency->status = $request->status;
        $currency->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.currencies');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $currency = Currency::find($id);
        $can_destroy = $this->canDestroy($currency->code);

        if($can_destroy['status']) {
            $currency->delete();
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy["status_message"]);
        }
        return redirect()->route('admin.currencies');
    }

    /**
     * Check the specified resource Can be deleted or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy($code)
    {
        if($code == global_settings('default_currency')) {
            return ['status' => false,'status_message' => Lang::get('messages.unable_to_delete_default_currency')];
        }

        if ($code == credentials("payment_currency","Stripe")) {
            return ['status' => false,'status_message' => Lang::get('messages.unable_to_delete_default_currency')];
        }

        if ($code == credentials("payment_currency","Paypal")) {
            return ['status' => false,'status_message' => Lang::get('messages.unable_to_delete_default_currency')];
        }

        $reservation_count = \App\Models\Reservation::where('currency_code',$code)->count();
        if($reservation_count > 0) {
            return ['status' => false,'status_message' => Lang::get('messages.this_currency_already_used')];
        }

        $payout_count = \App\Models\Payout::where('currency_code',$code)->count();
        if($payout_count > 0) {
            return ['status' => false,'status_message' => Lang::get('messages.this_currency_already_used')];
        }

        $hotel_count = \App\Models\HotelRoomPrice::where('currency_code',$code)->count();
        if($hotel_count > 0) {
            return ['status' => false,'status_message' => Lang::get('messages.this_currency_already_used')];
        }

        $coupon_code_count = \App\Models\CouponCode::where('currency_code',$code)->count();
        if($coupon_code_count > 0) {
            return ['status' => false,'status_message' => Lang::get('messages.this_currency_already_used')];
        }

        $penalty_count = \App\Models\UserPenalty::where('currency_code',$code)->count();
        if($penalty_count > 0) {
            return ['status' => false,'status_message' => Lang::get('messages.this_currency_already_used')];
        }
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
        $rules = array(
            'name' => 'required|max:25',
            'code' => 'required|unique:currencies,code,'.$id,
            'symbol' => 'required',
            'rate' => 'required',
            'status' => 'required',
        );

        $attributes = array(
            'name' => Lang::get('admin_messages.name'),
            'code' => Lang::get('admin_messages.code'),
            'symbol' => Lang::get('admin_messages.symbol'),
            'rate' => Lang::get('admin_messages.rate'),
            'status' => Lang::get('admin_messages.status'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
}
