<?php

/**
 * Static Page Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    StaticPageController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\StaticPagesDataTable;
use App\Models\StaticPage;
use App\Traits\Translatable;
use Lang;

class StaticPageController extends Controller
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
			['key' => 'content', 'title' => Lang::get('admin_messages.content'),'type' => 'textarea', 'class' => 'rich-text-editor','id' => 'rich-text-editor','rules' => 'required'],
		]);
		$this->view_data['main_title'] = Lang::get('admin_messages.manage_static_pages');
		$this->view_data['active_menu'] = 'pages';
		$this->view_data['sub_title'] = Lang::get('admin_messages.static_pages');
	}

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(StaticPagesDataTable $dataTable)
	{
		return $dataTable->render('admin.static_pages.view',$this->view_data);
	}

	/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create()
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.add_static_page');
		$this->view_data['result'] = $result = new StaticPage;
		$this->view_data['translations'] = $this->getTranslationsExceptDefault($result);

		return view('admin.static_pages.add', $this->view_data);
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

		$static_page = new StaticPage;
		$static_page->setLocale(global_settings('default_language'));
		$static_page->name = $request->name;
		$static_page->slug = \Str::slug($request->name);
		$static_page->content = $request->content;
		$static_page->must_agree = $request->must_agree;
		$static_page->in_footer = $request->in_footer;
		$static_page->under_section	= $request->under_section;
		$static_page->status = $request->status;
		$static_page->save();

		$this->updateTranslation($static_page,$request->translations ?? []);
		
		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
		return redirect()->route('admin.static_pages');
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function edit($id)
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.edit_static_page');
		$this->view_data['result'] = $result = StaticPage::findOrFail($id);
		$this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
		
		return view('admin.static_pages.edit', $this->view_data);
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

		$static_page = StaticPage::findOrFail($id);
		$static_page->setLocale(global_settings('default_language'));
		$static_page->name = $request->name;
		$static_page->slug = \Str::slug($request->name);
		$static_page->content = $request->content;
		$static_page->must_agree = $request->must_agree;
		$static_page->in_footer = $request->in_footer;
		$static_page->under_section	= $request->under_section;
		$static_page->status = $request->status;
		$static_page->save();

		$this->deleteTranslations($static_page,$request->removed_translations);
		$this->updateTranslation($static_page,$request->translations ?? []);
		
		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
		return redirect()->route('admin.static_pages');
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
			StaticPage::where('id',$id)->delete();
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy["status_message"]);
        }
        return redirect()->route('admin.static_pages');
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
			'content' => 'required',
			'must_agree' => 'required',
			'in_footer' => 'required',
			'status'    => 'required',
		);
		if($request_data->in_footer == 1) {
			$rules['under_section'] = 'required';
		}

		$attributes = array(
			'name'  		=> Lang::get('admin_messages.name'),
			'content'  		=> Lang::get('admin_messages.content'),
			'must_agree'  	=> Lang::get('admin_messages.must_agree'),
			'in_footer'  	=> Lang::get('admin_messages.footer'),
			'under_section' => Lang::get('admin_messages.under_section'),
			'status'    	=> Lang::get('admin_messages.status'),
		);
		
		$locales = array_keys($request_data->translations ?? []);
		$translation_rules = $this->getTranslationValidation($locales);

		$rules = array_merge($rules,$translation_rules['rules']);
		$attributes = array_merge($attributes,$translation_rules['attributes']);

		$this->validate($request_data,$rules,[],$attributes);
	}
}