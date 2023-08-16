<?php

/**
 * PopularCity Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    PopularCityController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\PopularCitiesDataTable;
use App\Models\PopularCity;
use Lang;

class PopularCityController extends Controller
{   
    /**
    * Constructor
    *
    */
    public function __construct()
    {
        $this->view_data['active_menu'] = 'popular_cities';
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_popular_cities');
        $this->view_data['sub_title'] = Lang::get('admin_messages.popular_cities');
    }

    /**
    * Display a hotel of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(PopularCitiesDataTable $dataTable)
    {
        return $dataTable->render('admin.popular_cities.view',$this->view_data);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_popular_city');
        $this->view_data['result'] = new PopularCity;
        return view('admin.popular_cities.add', $this->view_data);
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

        $popular_city = new PopularCity;
        
        $popular_city->name   = $request->name;
        $popular_city->address= $request->address;
        // $popular_city->latitude    = $request->latitude;
        // $popular_city->longitude   = $request->longitude;
        // $popular_city->place_id    = $request->place_id;
        $popular_city->country_code    = $request->country_code;
        // $popular_city->viewport    = $request->viewport;
        $popular_city->status      = $request->status;
        $popular_city->save();
        
        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
        $redirect_url = route('admin.popular_cities');
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
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_popular_city');
        $this->view_data['result'] = PopularCity::findOrFail($id);

        return view('admin.popular_cities.edit', $this->view_data);
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
        
        $popular_city = PopularCity::findOrFail($id);
        $popular_city->name   = $request->name;
        $popular_city->address= $request->address;
        // $popular_city->latitude    = $request->latitude;
        // $popular_city->longitude   = $request->longitude;
        // $popular_city->place_id    = $request->place_id;
        $popular_city->country_code    = $request->country_code;
        // $popular_city->viewport    = $request->viewport;
        $popular_city->status      = $request->status;
        $popular_city->save();
        
        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        $redirect_url = route('admin.popular_cities');
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
            $popular_cities = PopularCity::find($id);
            $popular_cities->delete();
        }

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        $redirect_url = route('admin.popular_cities');
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
            'name'     => 'required',
            'address'  => 'required',
            // 'latitude'      => 'required',
            // 'longitude'     => 'required',
            // 'place_id'      => 'required',
            'country_code' => 'required|exists:countries,name',
            'status'        => 'required',
        );
        $attributes = array(
            'name'     => Lang::get('admin_messages.display_name'),
            'address'  => Lang::get('admin_messages.address'),
            'country_code'  => Lang::get('admin_messages.country_code'),
            'status'        => Lang::get('admin_messages.status'),
        );
        $this->validate($request_data,$rules,[],$attributes);
    }
}