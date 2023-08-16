<?php

/**
 * Slider Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    SliderController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\SlidersDataTable;
use App\Models\Slider;
use App\Traits\Translatable;
use Lang;

class SliderController extends Controller
{
	use Translatable;

	/**
	* Constructor
	*
	*/
	public function __construct()
	{
		$this->view_data['translatable_fields'] = $this->translatable_fields = collect([
			['key' => 'title', 'title' => Lang::get('admin_messages.title'),'type' => 'text','rules' => 'required|max:100'],
			['key' => 'description', 'title' => Lang::get('admin_messages.description'),'type' => 'textarea','rules' => 'nullable|max:500'],
		]);
		$this->view_data['main_title'] = Lang::get('admin_messages.manage_home_page_sliders');
		$this->view_data['sub_title'] = Lang::get('admin_messages.home_page_sliders');
		$this->view_data['active_menu'] = 'sliders';
	}

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(SlidersDataTable $dataTable)
	{
		return $dataTable->render('admin.sliders.view',$this->view_data);
	}

	/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create()
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.add_home_page_sliders');
		$this->view_data['result'] = $result = new Slider;
		$this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
		return view('admin.sliders.add', $this->view_data);
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

		$slider = new Slider;
		$slider->setLocale(global_settings('default_language'));
		$upload_result = $this->uploadImage($request->file('image'),$slider->getUploadPath());
		if(!$upload_result['status']) {
			flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
			return redirect()->route('admin.sliders');
		}

		$slider->title = $request->title;
		$slider->description = $request->description ?? '';
		$slider->order_id = $request->order_id;
		$slider->image = $upload_result['file_name'];
		$slider->upload_driver = $upload_result['upload_driver'];
		$slider->status = $request->status;

		$slider->save();

		$this->updateTranslation($slider,$request->translations ?? []);

		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
		return redirect()->route('admin.sliders');
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function edit($id)
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.edit_home_page_sliders');
		$this->view_data['result'] = $result = Slider::findOrFail($id);
		$this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
		return view('admin.sliders.edit', $this->view_data);
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

		$slider = Slider::findOrFail($id);
		$slider->setLocale(global_settings('default_language'));
		$slider->title = $request->title;
		$slider->description = $request->description ?? '';
		$slider->order_id = $request->order_id;
		$slider->status = $request->status;
		
		if($request->hasFile('image')) {
			$slider->deleteImageFile();

			$upload_result = $this->uploadImage($request->file('image'),$slider->filePath);
			if(!$upload_result['status']) {
				flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
				return redirect()->route('admin.sliders');
			}

			$slider->image = $upload_result['file_name'];
			$slider->upload_driver = $upload_result['upload_driver'];
		}

		$slider->save();

		$this->deleteTranslations($slider,$request->removed_translations);
		$this->updateTranslation($slider,$request->translations ?? []);
		
		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
		return redirect()->route('admin.sliders');
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
			$slider = Slider::find($id);
			$slider->deleteImageFile();
			$slider->delete();
		}

		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
		
		return redirect()->route('admin.sliders');
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
		$image_data['name_prefix'] = 'home_slider_';
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
			'title'			=> 'required|max:50',
			'description'	=> 'nullable|max:500',
			'order_id'		=> 'required|integer|min:1',
			'image'			=> $image_rule.'mimes:'.view()->shared('valid_mimes').'|image|dimensions:min_width=600,min_height=400',
			'status'		=> 'required',
		);
		$attributes = array(
			'title'			=> Lang::get('admin_messages.title'),
			'description'	=> Lang::get('admin_messages.description'),
			'order_id'		=> Lang::get('admin_messages.order_id'),
			'image'			=> Lang::get('admin_messages.image'),
			'status'		=> Lang::get('admin_messages.status'),
		);

		$locales = array_keys($request_data->translations ?? []);
		$translation_rules = $this->getTranslationValidation($locales);

		$rules = array_merge($rules,$translation_rules['rules']);
		$attributes = array_merge($attributes,$translation_rules['attributes']);

		$this->validate($request_data,$rules,[],$attributes);
	}
}