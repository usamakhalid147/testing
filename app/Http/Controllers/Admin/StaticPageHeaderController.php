<?php

/**
 * Static Page Header Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    StaticPageHeaderController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StaticPageHeader;
use Lang;

class StaticPageHeaderController extends Controller
{
	/**
	* Constructor
	*
	*/
	public function __construct()
	{
		$this->view_data['main_title'] = Lang::get('admin_messages.static_page_header');
		$this->view_data['active_menu'] = 'static_page_header';
		$this->view_data['sub_title'] = Lang::get('admin_messages.static_page_header');
	}

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index()
	{
		$this->view_data['static_page_headers'] = $result = StaticPageHeader::all();
		
		return view('admin.static_page_headers.edit', $this->view_data);
	}

	/**
	* Update the specified resource in storage.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function update(Request $request)
	{
		$rules = [];
		$attributes = [];
		foreach($request->title ?? [] as $key => $title) {
			$rules['title.'.$key] = 'required';
			$attributes['title.'.$key] = Lang::get('admin_messages.title');
		}
		$validator = \Validator::make($request->all(), $rules, [], $attributes);

		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput();
		}

		foreach($request->title ?? [] as $key => $title) {
			$id = $key + 1;
			$static_page_header = StaticPageHeader::findOrFail($id);
			$static_page_header->title = ucwords(str_replace('_', ' ', $title));
			$static_page_header->display_name = $title;
			$static_page_header->save();
		}
		
		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
		return redirect()->route('admin.static_page_header');
	}
}