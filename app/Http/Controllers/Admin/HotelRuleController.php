<?php

/**
 * Hotel Rule Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    HotelRuleController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\HotelRulesDataTable;
use App\Models\HotelRule;
use App\Traits\Translatable;
use Lang;

class HotelRuleController extends Controller
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
			['key' => 'description', 'title' => Lang::get('admin_messages.description'),'type' => 'text','rules' => 'required'],
		]);
		$this->view_data['main_title'] = Lang::get('admin_messages.manage_hotel_rules');
		$this->view_data['active_menu'] = 'hotel_rules';
		$this->view_data['sub_title'] = Lang::get('admin_messages.hotel_rules');
		$this->base_path = 'admin.hotel_rules';
	}

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(HotelRulesDataTable $dataTable)
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
		$this->view_data['sub_title'] = Lang::get('admin_messages.add_hotel_rule');
		$this->view_data['result'] = $result = new HotelRule;
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

		$hotel_rule = new HotelRule;
		$hotel_rule->setLocale(global_settings('default_language'));
		$hotel_rule->name = $request->name;
		$hotel_rule->description = $request->description;
		$hotel_rule->status = $request->status;
		$hotel_rule->save();

		$this->updateTranslation($hotel_rule,$request->translations ?? []);
		
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
		$this->view_data['sub_title'] = Lang::get('admin_messages.edit_hotel_rule');
		$this->view_data['result'] = $result = HotelRule::findOrFail($id);
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

		$hotel_rule = HotelRule::findOrFail($id);
		$hotel_rule->setLocale(global_settings('default_language'));
		$hotel_rule->name = $request->name;
		$hotel_rule->description = $request->description;
		$hotel_rule->status = $request->status;
		$hotel_rule->save();

		$this->deleteTranslations($hotel_rule,$request->removed_translations);
		$this->updateTranslation($hotel_rule,$request->translations ?? []);
		
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
			HotelRule::where('id',$id)->delete();
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