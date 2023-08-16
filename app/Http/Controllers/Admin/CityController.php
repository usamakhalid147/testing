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
use App\DataTables\CitiesDataTable;
use App\Models\HotelAddress;
use App\Models\City;
use Lang;

class CityController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_cities');
        $this->view_data['sub_title'] =Lang::get('admin_messages.cities');
        $this->view_data['active_menu'] = 'cities';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CitiesDataTable $dataTable)
    {
        return $dataTable->render('admin.cities.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_city');
        $this->view_data['result'] = new City;
        return view('admin.cities.add', $this->view_data);
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
        $city = new City;
        $city->country = $request->country;
        $city->roman_number = $request->roman_number;
        $city->name = $request->name;
        $city->status = $request->status;
        $city->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
        return redirect()->route('admin.cities');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_city');
        $this->view_data['result'] = City::findOrFail($id);
        return view('admin.cities.edit', $this->view_data);
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

        $city = City::find($id);
        $city->country = $request->country;
        $city->roman_number = $request->roman_number;
        $city->name = $request->name;
        $city->status = $request->status;
        $city->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.cities');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::find($id);
        $can_destroy = $this->canDestroy($city->name);
        
        if($can_destroy['status']) {
            $city->delete();
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy["status_message"]);
        }
        return redirect()->route('admin.cities');
    }

    /**
     * Check the specified resource Can be deleted or not.
     *
     * @param  string  $name
     * @return Array
     */
    protected function canDestroy($name)
    {
        $room_count = \App\Models\HotelAddress::where('city',$name)->count();
        if($room_count > 0) {
            return ['status' => false,'status_message' => Lang::get('messages.this_city_already_used')];
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
            'name' => 'required|unique:cities,name,'.$id,
            'roman_number' => 'required|unique:cities,roman_number,'.$id,
            'country' => 'required',
            'status' => 'required',
        );
        $attributes = array(
            'name'      => Lang::get('admin_messages.name'),
            'roman_number' => Lang::get('admin_messages.roman_number'),
            'country' => Lang::get('admin_messages.country'),
            'status' => Lang::get('admin_messages.status'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
}
