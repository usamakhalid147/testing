<?php

/**
 * Language Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    LanguageController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\LanguagesDataTable;
use App\Models\Language;
use Lang;

class LanguageController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_languages');
        $this->view_data['active_menu'] = 'languages';
        $this->view_data['sub_title']  = Lang::get('admin_messages.languages');
        
		$this->translatableModels = [
            'Amenity',
            'AmenityType',
            'CommunityBanner',
            'HelpCategory',
            'Help',
            'Meta',
            'Slider',
            'HotelRule',
            'PropertyType',
            'RoomType',
            'StaticPage',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LanguagesDataTable $dataTable)
    {
        return $dataTable->render('admin.languages.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_language');
        $this->view_data['result'] = new Language;
        return view('admin.languages.add', $this->view_data);
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

        $validated = $request->only(['code','name','is_translatable','status']);

        Language::create($validated);

        flashMessage('success',Lang::get('admin_messages.success'), Lang::get('admin_messages.successfully_added'));
        return redirect()->route('admin.languages');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_language');
        $this->view_data['result'] = Language::findOrFail($id);
        return view('admin.languages.edit', $this->view_data);
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

        $language = Language::findOrFail($id);

        if ($request->status == '0') {
            if($language->code == global_settings('default_language')) {
                flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('messages.unable_to_delete_default_language'));
                return redirect()->route('admin.languages');
            }
        }
        
        if($language->code != $request->code) {
            $this->updateLocale($language->code,$request->code);
        }

        $language->code = $request->code;
        $language->name = $request->name;
        $language->is_translatable = $request->is_translatable;
        $language->status = $request->status;
        $language->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.languages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $language = Language::find($id);
        $can_destroy = $this->canDestroy($language->code);
        
        if($can_destroy['status']) {
           Language::where('id',$id)->delete();
           flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy["status_message"]);
        }
        return redirect()->route('admin.languages');
    }

    /**
     * Check the specified resource Can be deleted or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy($code)
    {
        if($code == global_settings('default_language')) {
            return ['status' => false,'status_message' => Lang::get('messages.unable_to_delete_default_language')];
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
        $rules = array(
            'code'=> 'required|max:3|unique:languages,code,'.$id,
            'name'      => 'required|max:25',
            'is_translatable'    => 'required',
            'status'    => 'required',
        );

        $attributes = array(
            'code' => Lang::get('admin_messages.code'),
            'name' => Lang::get('admin_messages.name'),
            'is_translatable' => Lang::get('admin_messages.is_translatable'),
            'status' => Lang::get('admin_messages.status'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
}