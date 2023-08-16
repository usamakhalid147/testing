<?php

/**
 * Country Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    CountryController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\CountriesDataTable;
use App\Models\Country;
use Lang;

class CountryController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_countries');
        $this->view_data['sub_title'] =Lang::get('admin_messages.countries');
        $this->view_data['active_menu'] = 'countries';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CountriesDataTable $dataTable)
    {
        return $dataTable->render('admin.countries.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_country');
        $this->view_data['result'] = new Country;
        return view('admin.countries.add', $this->view_data);
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

        $country = new country;
        $country->name = $request->name;
        $country->full_name = $request->full_name;
        $country->iso3 = $request->iso3;
        $country->numcode = $request->numcode;
        $country->phone_code = $request->phone_code;
        $country->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
        return redirect()->route('admin.countries');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_country');
        $this->view_data['result'] = Country::findOrFail($id);
        return view('admin.countries.edit', $this->view_data);
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

        $country = Country::Find($id);
        $country->name = $request->name;
        $country->full_name = $request->full_name;
        $country->iso3 = $request->iso3;
        $country->numcode = $request->numcode;
        $country->phone_code = $request->phone_code;
        $country->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.countries');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $country = Country::find($id);
        $can_destroy = $this->canDestroy($country->name);
        
        if($can_destroy['status']) {
            $country->delete();
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy["status_message"]);
        }
        return redirect()->route('admin.countries');
    }

    /**
     * Check the specified resource Can be deleted or not.
     *
     * @param  string  $name
     * @return Array
     */
    protected function canDestroy($name)
    {
        $room_count = \App\Models\RoomAddress::where('country_code',$name)->count();
        if($room_count > 0) {
            return ['status' => false,'status_message' => Lang::get('messages.this_country_already_used')];
        }

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
            'name' => 'required|max:5|unique:countries,name,'.$id,
            'full_name' => 'required',
            'iso3' => 'required|unique:countries,iso3,'.$id,
            'numcode' => 'required',
            'phone_code' => 'required',
        );

        $attributes = array(
            'name'      => Lang::get('admin_messages.code'),
            'full_name' => Lang::get('admin_messages.full_name'),
            'iso3'      => Lang::get('admin_messages.iso3'),
            'numcode'   => Lang::get('admin_messages.numcode'),
            'phone_code' => Lang::get('admin_messages.phone_code'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
}
