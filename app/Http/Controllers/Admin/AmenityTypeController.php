<?php

/**
 * Amenity Type Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    AmenityTypeController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\AmenityTypesDataTable;
use App\Models\Amenity;
use App\Models\AmenityType;
use App\Traits\Translatable;
use Lang;

class AmenityTypeController extends Controller
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
			['key' => 'description', 'title' => Lang::get('admin_messages.description'),'type' => 'text','rules' => ''],
		]);
		$this->view_data['main_title'] = Lang::get('admin_messages.manage_amenity_types');
		$this->view_data['active_menu'] = 'amenity_types';
		$this->view_data['sub_title'] = Lang::get('admin_messages.amenity_types');
		$this->base_path = 'admin.amenity_types';
	}

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(AmenityTypesDataTable $dataTable)
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
		$this->view_data['sub_title'] = Lang::get('admin_messages.add_amenity_type');
		$this->view_data['result'] = $result = new AmenityType;
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

		$amenity_type = new AmenityType;
		$amenity_type->setLocale(global_settings('default_language'));
		$amenity_type->name = $request->name;
		$amenity_type->description = $request->description;
		$amenity_type->status = $request->status;
		$amenity_type->save();

		$this->updateTranslation($amenity_type,$request->translations ?? []);
		
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
		$this->view_data['sub_title'] = Lang::get('admin_messages.edit_amenity_type');
		$this->view_data['result'] = $result = AmenityType::findOrFail($id);
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

		$amenity_type = AmenityType::findOrFail($id);
		$amenity_type->setLocale(global_settings('default_language'));
		$amenity_type->name = $request->name;
		$amenity_type->description = $request->description;
		$amenity_type->status = $request->status;
		$amenity_type->save();

		$this->deleteTranslations($amenity_type,$request->removed_translations);
		$this->updateTranslation($amenity_type,$request->translations ?? []);
		
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
		$amenity_type = Amenity::where('amenity_type_id',$id)->get();
		if ($amenity_type->count() > 0) {
            flashMessage('danger',Lang::get('messages.this_amenity_type_has_some_rooms'),$can_destroy["status_message"]);
        	return redirect()->route($this->base_path);
		}
		if($can_destroy["status"]) {
			AmenityType::where('id',$id)->delete();
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy["status_message"]);
        }
        return redirect()->route($this->base_path);
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
			'name' => 'required|max:30',
			'description' => 'required',
			'status' => 'required',
		);

		$attributes = array(
			'name' => Lang::get('admin_messages.name'),
			'description' => Lang::get('admin_messages.description'),
			'status' => Lang::get('admin_messages.status'),
		);
		
		$locales = array_keys($request_data->translations ?? []);
		$translation_rules = $this->getTranslationValidation($locales);

		$rules = array_merge($rules,$translation_rules['rules']);
		$attributes = array_merge($attributes,$translation_rules['attributes']);

		$this->validate($request_data,$rules,[],$attributes);
	}
}