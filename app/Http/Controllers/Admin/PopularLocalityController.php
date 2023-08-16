<?php

/**
 * PopularLocality Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    PopularLocalityController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\PopularLocalitiesDataTable;
use App\Models\PopularLocality;
use App\Models\PopularCity;
use Lang;

class PopularLocalityController extends Controller
{ 
    /**
    * Constructor
    *
    */
    public function __construct()
    {
        $this->view_data['active_menu'] = 'popular_localities';
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_popular_localities');
        $this->view_data['sub_title'] = Lang::get('admin_messages.popular_localities');
    }

    /**
    * Display a hotel of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(PopularLocalitiesDataTable $dataTable)
    {
        return $dataTable->render('admin.popular_localities.view',$this->view_data);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_popular_locality');
        $this->view_data['popular_cities'] = PopularCity::get()->pluck('name','id');
        $this->view_data['result'] = new PopularLocality;
        return view('admin.popular_localities.add', $this->view_data);
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

        $popular_locality = new PopularLocality;
        
        $popular_locality->name   = $request->name;
        $popular_locality->popular_city_id = $request->popular_city_id;
        $popular_locality->address= $request->address;
        $popular_locality->latitude    = $request->latitude;
        $popular_locality->longitude   = $request->longitude;
        $popular_locality->place_id    = $request->place_id;
        $popular_locality->country_code    = $request->country_code;
        $popular_locality->status      = $request->status;
        $popular_locality->save();
        
        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
        $redirect_url = route('admin.popular_localities');
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
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_popular_locality');
        $this->view_data['popular_cities'] = PopularCity::get()->pluck('name','id');
        $this->view_data['result'] = PopularLocality::findOrFail($id);

        return view('admin.popular_localities.edit', $this->view_data);
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
        
        $popular_locality = PopularLocality::findOrFail($id);
        $popular_locality->name   = $request->name;
        $popular_locality->popular_city_id = $request->popular_city_id;
        $popular_locality->address= $request->address;
        $popular_locality->latitude    = $request->latitude;
        $popular_locality->longitude   = $request->longitude;
        $popular_locality->place_id    = $request->place_id;
        $popular_locality->country_code    = $request->country_code;
        $popular_locality->status      = $request->status;
        $popular_locality->save();
        
        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        $redirect_url = route('admin.popular_localities');
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
            $popular_localities = PopularLocality::find($id);
            $popular_localities->delete();
        }

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        $redirect_url = route('admin.popular_localities');
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
            'latitude'      => 'required',
            'longitude'     => 'required',
            'place_id'      => 'required',
            'popular_city_id' => 'required',
            'country_code' => 'required|exists:countries,name',
            'status'        => 'required',
        );
        $attributes = array(
            'name'     => Lang::get('admin_messages.display_name'),
            'address'  => Lang::get('admin_messages.address'),
            'popular_city_id' => Lang::get('admin_messages.popular_city'),
            'country_code'  => Lang::get('admin_messages.country_code'),
            'status'        => Lang::get('admin_messages.status'),
        );
        $this->validate($request_data,$rules,[],$attributes);
    }
}
