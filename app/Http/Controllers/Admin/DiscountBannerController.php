<?php

/**
 * DiscountBanner Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    DiscountBannerController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\DiscountBannersDataTable;
use App\Models\DiscountBanner;
use Lang;

class DiscountBannerController extends Controller
{
	/**
	* Constructor
	*
	*/
	public function __construct()
	{
		$this->view_data['main_title'] = Lang::get('admin_messages.manage_discount_banners');
		$this->view_data['sub_title'] = Lang::get('admin_messages.discount_banners');
		$this->view_data['active_menu'] = 'discount_banners';
	}

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(DiscountBannersDataTable $dataTable)
	{
		return $dataTable->render('admin.discount_banners.view',$this->view_data);
	}

	/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create()
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.add_banner');
		$this->view_data['result'] = $result = new DiscountBanner;
		return view('admin.discount_banners.add', $this->view_data);
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function edit($id)
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.edit_banner');
		$this->view_data['result'] = $result = DiscountBanner::findOrFail($id);
		return view('admin.discount_banners.edit', $this->view_data);
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

		$discount_banner = DiscountBanner::findOrFail($id);
		$discount_banner->order_id = $request->order_id;
		$discount_banner->status = $request->status;
		
		if($request->hasFile('image')) {
			$discount_banner->deleteImageFile();

			$upload_result = $this->uploadImage($request->file('image'),$discount_banner->filePath);
			if(!$upload_result['status']) {
				flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
				return redirect()->route('admin.discount_banners');
			}

			$discount_banner->image = $upload_result['file_name'];
			$discount_banner->upload_driver = $upload_result['upload_driver'];
		}

		$discount_banner->save();
		
		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
		return redirect()->route('admin.discount_banners');
	}

	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function destroy($id)
	{
		$discount_banner = DiscountBanner::findOrFail($id);
		$can_destroy = $this->canDestroy($id);
		if($can_destroy['status']) {
			$discount_banner->deleteImageFile();
			$discount_banner->delete();
		}

		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
		
		return redirect()->route('admin.discount_banners');
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
		$image_data['name_prefix'] = 'discount_banner_';
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