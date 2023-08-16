<?php

/**
 * Property Type Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    PropertyTypeController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\PropertyTypesDataTable;
use App\Models\PropertyType;
use App\Traits\Translatable;
use Lang;

class PropertyTypeController extends Controller
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
			['key' => 'description', 'title' => Lang::get('admin_messages.description'),'type' => 'text'],
		]);
		$this->view_data['main_title'] = Lang::get('admin_messages.manage_property_types');
		$this->view_data['active_menu'] = 'property_types';
		$this->view_data['sub_title'] = Lang::get('admin_messages.property_types');
		$this->base_path = 'admin.property_types';
	}

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(PropertyTypesDataTable $dataTable)
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
		$this->view_data['sub_title'] = Lang::get('admin_messages.add_property_type');
		$this->view_data['result'] = $result = new PropertyType;
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

		$property_type = new PropertyType;
		$property_type->setLocale(global_settings('default_language'));

		$upload_result = $this->uploadImage($request->file('image'),$property_type->filePath);
		if(!$upload_result['status']) {
			flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
			return redirect()->route($this->base_path);
		}

		$property_type->name = $request->name;
		$property_type->description = $request->description;
		$property_type->image = $upload_result['file_name'];
		$property_type->upload_driver = $upload_result['upload_driver'];
		$property_type->status = $request->status;
		$property_type->save();

		$this->updateTranslation($property_type,$request->translations ?? []);
		
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
		$this->view_data['sub_title'] = Lang::get('admin_messages.edit_property_type');
		$this->view_data['result'] = $result = PropertyType::findOrFail($id);
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

		$property_type = PropertyType::findOrFail($id);
		$property_type->setLocale(global_settings('default_language'));
		$property_type->name = $request->name;
		$property_type->description = $request->description;

		if($request->hasFile('image')) {
			$property_type->deleteImageFile();

			$upload_result = $this->uploadImage($request->file('image'),$property_type->filePath);
			if(!$upload_result['status']) {
				flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
				return redirect()->route($this->base_path);
			}

			$property_type->image = $upload_result['file_name'];
			$property_type->upload_driver = $upload_result['upload_driver'];
		}

		$property_type->status = $request->status;
		$property_type->save();

		$this->deleteTranslations($property_type,$request->removed_translations);
		$this->updateTranslation($property_type,$request->translations ?? []);
		
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
		$property_type = \App\Models\Hotel::where('property_type',$id)->get();
		if ($property_type->count() > 0) {
            flashMessage('danger',Lang::get('messages.this_property_type_has_some_rooms'),$can_destroy["status_message"]);
        	return redirect()->route($this->base_path);
		}
		if($can_destroy["status"]) {
			PropertyType::where('id',$id)->delete();
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
		$image_data['name_prefix'] = 'property_type_';
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
			'name' => 'required|max:30',
			'description' => 'required',
			'image' => $image_rule.'mimes:'.view()->shared('valid_mimes'),
			'status' => 'required',
		);

		$attributes = array(
			'name' => Lang::get('admin_messages.name'),
			'description' => Lang::get('admin_messages.description'),
			'image' => Lang::get('admin_messages.image'),
			'status' => Lang::get('admin_messages.status'),
		);
		
		$locales = array_keys($request_data->translations ?? []);
		$translation_rules = $this->getTranslationValidation($locales);

		$rules = array_merge($rules,$translation_rules['rules']);
		$attributes = array_merge($attributes,$translation_rules['attributes']);

		$this->validate($request_data,$rules,[],$attributes);
	}
}