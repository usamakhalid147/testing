<?php

/**
 * Featured City Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    FeaturedCityController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\FeaturedCitiesDataTable;
use App\Models\FeaturedCity;
use Lang;

class FeaturedCityController extends Controller
{	
	/**
	* Constructor
	*
	*/
	public function __construct()
	{
		$this->view_data['active_menu'] = 'featured_cities';
		$this->view_data['main_title'] = Lang::get('admin_messages.manage_featured_cities');
		$this->view_data['sub_title'] = Lang::get('admin_messages.featured_cities');
	}

	/**
	 * Upload Given Image to Server
	 *
	 * @return Array Upload Result
	 */
    protected function uploadImage($image, $target_dir)
    {
    	$image_handler = resolve('App\Contracts\ImageHandleInterface');
		$image_data = array();
		$image_data['name_prefix'] = 'featured_city_';
		$image_data['add_time'] = true;
		$image_data['target_dir'] = $target_dir;

        return $image_handler->upload($image,$image_data);
    }

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(FeaturedCitiesDataTable $dataTable)
	{
		return $dataTable->render('admin.featured_cities.view',$this->view_data);
	}

	/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create()
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.add_featured_city');
		$this->view_data['result'] = new FeaturedCity;
		return view('admin.featured_cities.add', $this->view_data);
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
		
		$featured_city = new FeaturedCity;
		$upload_result = $this->uploadImage($request->file('image'),$featured_city->getUploadPath());
		if(!$upload_result['status']) {
			flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
			return redirect()->route('admin.featured_cities');
		}

		$featured_city->city_name	= $request->city_name;
		// $featured_city->latitude	= $request->latitude;
		// $featured_city->longitude	= $request->longitude;
		// $featured_city->place_id	= $request->place_id;
		$featured_city->order_id	= $request->order_id;
		$featured_city->display_name= $request->display_name;
		$featured_city->image       = $upload_result['file_name'];
		$featured_city->upload_driver = $upload_result['upload_driver'];
		$featured_city->status		= $request->status;
		$featured_city->save();
		
		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
		return redirect()->route('admin.featured_cities');
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function edit($id)
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.edit_featured_city');
		$this->view_data['result'] = FeaturedCity::findOrFail($id);

		return view('admin.featured_cities.edit', $this->view_data);
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
		
		$featured_city = FeaturedCity::findOrFail($id);
		$featured_city->city_name	= $request->city_name;
		// $featured_city->latitude	= $request->latitude;
		// $featured_city->longitude	= $request->longitude;
		// $featured_city->place_id	= $request->place_id;
		$featured_city->order_id	= $request->order_id;
		$featured_city->display_name= $request->display_name;
		$featured_city->status		= $request->status;
		
		if($request->hasFile('image')) {
			$featured_city->deleteImageFile();
			
			$upload_result = $this->uploadImage($request->file('image'),$featured_city->filePath);
			if(!$upload_result['status']) {
				flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
				return redirect()->route('admin.featured_cities');
			}
			$featured_city->image = $upload_result['file_name'];
			$featured_city->upload_driver = $upload_result['upload_driver'];
		}

		$featured_city->save();

		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
		return redirect()->route('admin.featured_cities');
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
			$featured_city = FeaturedCity::find($id);
			$featured_city->deleteImageFile();
			$featured_city->delete();
		}

		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
		return redirect()->route('admin.featured_cities');
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
		$image_rule = ($id == '') ? 'required|':'';
		$rules = array(
			'city_name'     => 'required',
			'display_name'  => 'required',
			// 'latitude'   	=> 'required',
			// 'longitude'   	=> 'required',
			// 'place_id'   	=> 'required',
			'image'         => $image_rule.'mimes:'.view()->shared('valid_mimes'),
			'status'        => 'required',
		);
		$attributes = array(
			'city_name'		=> Lang::get('admin_messages.city_name'),
			'display_name'	=> Lang::get('admin_messages.display_name'),
			'image'			=> Lang::get('admin_messages.image'),
			'status'		=> Lang::get('admin_messages.status'),
		);
		$this->validate($request_data,$rules,[],$attributes);
	}
}