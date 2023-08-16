<?php

/**
 * Login Slider Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    LoginSliderController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\LoginSlidersDataTable;
use App\Models\LoginSlider;
use Lang;

class LoginSliderController extends Controller
{
	/**
	* Constructor
	*
	*/
	public function __construct()
	{
		$this->view_data['main_title'] = Lang::get('admin_messages.manage_login_sliders');
		$this->view_data['sub_title'] = Lang::get('admin_messages.login_sliders');
		$this->view_data['active_menu'] = 'login_sliders';
	}

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(LoginSlidersDataTable $dataTable)
	{
		return $dataTable->render('admin.login_sliders.view',$this->view_data);
	}

	/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create()
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.add_slider');
		$this->view_data['result'] = $result = new LoginSlider;
		return view('admin.login_sliders.add', $this->view_data);
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

		$login_slider = new LoginSlider;

		$upload_result = $this->uploadImage($request->file('image'),$login_slider->getUploadPath());
		if(!$upload_result['status']) {
			flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
			return redirect()->route('admin.login_sliders');
		}

		$login_slider->order_id = $request->order_id;
		$login_slider->image = $upload_result['file_name'];
		$login_slider->upload_driver = $upload_result['upload_driver'];
		$login_slider->status = $request->status;

		$login_slider->save();

		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
		return redirect()->route('admin.login_sliders');
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function edit($id)
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.edit_slider');
		$this->view_data['result'] = $result = LoginSlider::findOrFail($id);
		return view('admin.login_sliders.edit', $this->view_data);
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

		$login_slider = LoginSlider::findOrFail($id);
		$login_slider->order_id = $request->order_id;
		$login_slider->status = $request->status;
		
		if($request->hasFile('image')) {
			$login_slider->deleteImageFile();

			$upload_result = $this->uploadImage($request->file('image'),$login_slider->filePath);
			if(!$upload_result['status']) {
				flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
				return redirect()->route('admin.login_sliders');
			}

			$login_slider->image = $upload_result['file_name'];
			$login_slider->upload_driver = $upload_result['upload_driver'];
		}

		$login_slider->save();
		
		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
		return redirect()->route('admin.login_sliders');
	}

	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function destroy($id)
	{
		$login_slider = LoginSlider::findOrFail($id);
		$can_destroy = $this->canDestroy($id);
		if($can_destroy['status']) {
			$login_slider->deleteImageFile();
			$login_slider->delete();
		}

		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
		
		return redirect()->route('admin.login_sliders');
	}

	/**
	 * Upload Given Image to Server
	 *
	 * @return Array Upload Result
	 */
	protected function uploadImage($image,$target_dir)
	{
		$image_handler = resolve('App\Contracts\ImageHandleInterface');
		$image_data = array();
		$image_data['name_prefix'] = 'admin_slider_';
		$image_data['add_time'] = true;
		$image_data['target_dir'] = $target_dir;

		return $image_handler->upload($image,$image_data);
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
			'order_id'		=> 'required|integer|min:1',
			'image'			=> $image_rule.'mimes:'.view()->shared('valid_mimes'),
			'status'		=> 'required',
		);
		$attributes = array(
			'order_id'		=> Lang::get('admin_messages.order_id'),
			'image'			=> Lang::get('admin_messages.image'),
			'status'		=> Lang::get('admin_messages.status'),
		);

		$this->validate($request_data,$rules,[],$attributes);
	}
}