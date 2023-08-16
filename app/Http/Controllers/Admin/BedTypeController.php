<?php

/**
 * Bed Type Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    BedTypeController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\BedTypesDataTable;
use App\Models\BedType;
use App\Traits\Translatable;
use Lang;

class BedTypeController extends Controller
{
	use Translatable;

	/**
	* Constructor
	*
	*/
	public function __construct()
	{
		$this->view_data['translatable_fields'] = $this->translatable_fields = collect([
			['key' => 'name', 'title' => Lang::get('admin_messages.name'),'type' => 'text','rules' => 'required|max:30'],
		]);
		$this->view_data['main_title'] = Lang::get('admin_messages.manage_bed_types');
		$this->view_data['active_menu'] = 'bed_types';
		$this->view_data['sub_title'] = Lang::get('admin_messages.bed_types');
		$this->base_path = 'admin.bed_types';
	}

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(BedTypesDataTable $dataTable)
	{
		return $dataTable->render($this->base_path.'.view',$this->view_data);
	}

	/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create()
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.add_bed_type');
		$this->view_data['result'] = $result = new BedType;
		$this->view_data['translations'] = $this->getTranslationsExceptDefault($result);

		return view($this->base_path.'.add', $this->view_data);
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

		$bed_type = new BedType;
		$bed_type->setLocale(global_settings('default_language'));

		$upload_result = $this->uploadImage($request->file('image'),$bed_type->filePath);
		if(!$upload_result['status']) {
			flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
			return redirect()->route($this->base_path);
		}

		$bed_type->name = $request->name;
		$bed_type->image = $upload_result['file_name'];
		$bed_type->upload_driver = $upload_result['upload_driver'];
		$bed_type->is_show_extra_price = $request->is_show_extra_price;
		$bed_type->status = $request->status;
		$bed_type->save();

		$this->updateTranslation($bed_type,$request->translations ?? []);
		
		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
		return redirect()->route($this->base_path);
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function edit($id)
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.edit_bed_type');
		$this->view_data['result'] = $result = BedType::findOrFail($id);
		$this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
		
		return view($this->base_path.'.edit', $this->view_data);
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

		$bed_type = BedType::findOrFail($id);
		$bed_type->setLocale(global_settings('default_language'));
		$bed_type->name = $request->name;

		if($request->hasFile('image')) {
			$bed_type->deleteImageFile();

			$upload_result = $this->uploadImage($request->file('image'),$bed_type->filePath);
			if(!$upload_result['status']) {
				flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
				return redirect()->route($this->base_path);
			}

			$bed_type->image = $upload_result['file_name'];
			$bed_type->upload_driver = $upload_result['upload_driver'];
		}

		$bed_type->is_show_extra_price = $request->is_show_extra_price;
		$bed_type->status = $request->status;
		$bed_type->save();

		$this->deleteTranslations($bed_type,$request->removed_translations);
		$this->updateTranslation($bed_type,$request->translations ?? []);
		
		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
		return redirect()->route($this->base_path);
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

		if($can_destroy["status"]) {
			BedType::where('id',$id)->delete();
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy["status_message"]);
        }
        return redirect()->route($this->base_path);
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
		$image_data['name_prefix'] = 'bed_type_';
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
		$room_bed_count = \App\Models\HotelRoomBed::where('bed_room',$id)->count();
		if ($room_bed_count > 0) {
        	return ['status' => false,'status_message' => Lang::get('messages.this_bed_type_has_some_rooms')];
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
		$image_rule = ($id == '') ? 'required|':'';
		$rules = array(
			'name' => 'required|max:30',
			'image' => $image_rule.'mimes:'.view()->shared('valid_mimes'),
			'is_show_extra_price' => 'required',
			'status' => 'required',
		);

		$attributes = array(
			'name' => Lang::get('admin_messages.name'),
			'image' => Lang::get('admin_messages.image'),
			'is_show_extra_price' => Lang::get('admin_messages.is_show_extra_price'),
			'status' => Lang::get('admin_messages.status'),
		);
		
		$locales = array_keys($request_data->translations ?? []);
		$translation_rules = $this->getTranslationValidation($locales);

		$rules = array_merge($rules,$translation_rules['rules']);
		$attributes = array_merge($attributes,$translation_rules['attributes']);

		$this->validate($request_data,$rules,[],$attributes);
	}
}