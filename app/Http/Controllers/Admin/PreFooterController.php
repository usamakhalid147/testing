<?php

/**
 * Pre Footer Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    PreFooterController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\PreFooterDataTable;
use App\Models\PreFooter;
use App\Models\PreFooterValue;
use App\Traits\Translatable;
use Lang;

class PreFooterController extends Controller
{
	use Translatable;

	/**
	* Constructor
	*
	*/
	public function __construct()
	{
		$this->view_data['translatable_fields'] = $this->translatable_fields = collect([
			['key' => 'title', 'title' => Lang::get('admin_messages.title'),'type' => 'text','rules' => 'required'],
			['key' => 'description', 'title' => Lang::get('admin_messages.description'),'type' => 'textarea'],
		]);
		$this->view_data['main_title'] = Lang::get('admin_messages.manage_pre_footers');
		$this->view_data['sub_title'] = Lang::get('admin_messages.pre_footers');
		$this->view_data['active_menu'] = 'pre_footers';
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
		$image_data['name_prefix'] = 'pre_footer_';
		$image_data['add_time'] = true;
		$image_data['target_dir'] = $target_dir;

		return $image_handler->upload($image,$image_data);
	}

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(PreFooterDataTable $dataTable)
	{
		return $dataTable->render('admin.pre_footers.view',$this->view_data);
	}

	/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create()
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.add_pre_footer');
		$this->view_data['result'] = $result = new PreFooter;
		$this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
		return view('admin.pre_footers.add', $this->view_data);
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

		$pre_footer = new PreFooter;
		$pre_footer->setLocale(global_settings('default_language'));

		$pre_footer->title      = $request->title ?? '';
		$pre_footer->description= $request->description ?? '';
		$pre_footer->status     = $request->status;

		$pre_footer->save();

		$this->updateTranslation($pre_footer,$request->translations ?? []);

		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
		return redirect()->route('admin.pre_footers');
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function edit($id)
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.edit_pre_footer');
		$this->view_data['result'] = $result = PreFooter::findOrFail($id);
		$this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
		return view('admin.pre_footers.edit', $this->view_data);
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

		$pre_footer = PreFooter::findOrFail($id);
		$pre_footer->setLocale(global_settings('default_language'));
		$pre_footer->title		= $request->title ?? '';
		$pre_footer->description= $request->description ?? '';
		$pre_footer->status		= $request->status;
		
		$pre_footer->save();

		$this->deleteTranslations($pre_footer,$request->removed_translations);
		$this->updateTranslation($pre_footer,$request->translations ?? []);
	
		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
		return redirect()->route('admin.pre_footers');
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
			$pre_footer = PreFooter::find($id);
		}

		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
		
		return redirect()->route('admin.pre_footers');
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
		$valid_mimes = view()->shared('valid_mimes');
		$rules = array(
			'title'	=> 'required',
			'description'	=> 'required',
			'status'		=> 'required',
		);
		$attributes = array(
			'title'			=> 'Title',
			'description'	=> 'Description',
			'status'		=> 'Status',
		);

		$locales = array_keys($request_data->translations ?? []);
		$translation_rules = $this->getTranslationValidation($locales);

		$rules = array_merge($rules,$translation_rules['rules']);
		$attributes = array_merge($attributes,$translation_rules['attributes']);

		$this->validate($request_data,$rules,[],$attributes);
	}
}