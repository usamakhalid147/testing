<?php

/**
 * Help Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    HelpController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\HelpsDataTable;
use App\Models\Help;
use App\Traits\Translatable;
use Lang;

class HelpController extends Controller
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
            ['key' => 'content', 'title' => Lang::get('admin_messages.content'),'type' => 'textarea', 'class' => 'rich-text-editor','id' => 'rich-text-editor','rules' => 'required'],
        ]);
        $this->view_data['active_menu'] = 'helps';
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_helps');
        $this->view_data['sub_title'] = Lang::get('admin_messages.helps');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HelpsDataTable $dataTable)
    {
        return $dataTable->render('admin.helps.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_help');
        $this->view_data['result'] = $result = new Help;
        $this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
        $this->view_data['categories'] = resolve('HelpCategory')->where('status','1')->pluck('title','id');
        return view('admin.helps.add', $this->view_data);
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

        $help = new Help;
        $help->setLocale(global_settings('default_language'));
        $help->title = $request->title;
        $help->slug = $request->slug ?? \Str::slug($request->title);
        $help->content = $request->content;
        $help->category_id = $request->category;
        $help->is_recommended = $request->is_recommended;
        $help->status = $request->status;
        $help->save();

        $this->updateTranslation($help,$request->translations ?? []);
        
        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
        return redirect()->route('admin.helps');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_help');
        $this->view_data['result'] = $result = Help::findOrFail($id);
        $this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
        $this->view_data['categories'] = resolve('HelpCategory')->pluck('title','id');
        return view('admin.helps.edit', $this->view_data);
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

        $help = Help::findOrFail($id);
        $help->setLocale(global_settings('default_language'));
        $help->title = $request->title;
        $help->slug = $request->slug ?? \Str::slug($request->title);
        $help->content = $request->content;
        $help->category_id = $request->category;
        $help->is_recommended = $request->is_recommended;
        $help->status = $request->status;
        $help->save();
        
        $this->deleteTranslations($help,$request->removed_translations);
        $this->updateTranslation($help,$request->translations ?? []);
        
        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.helps');
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
            Help::where('id',$id)->delete();
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy["status_message"]);
        }
        return redirect()->route('admin.helps');
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
            'title' => 'required',
            'content' => 'required',
            'category' => 'required',
            'is_recommended' => 'required',
            'status'    => 'required',
        );

        $attributes = array(
            'title' => Lang::get('admin_messages.title'),
            'content' => Lang::get('admin_messages.content'),
            'category' => Lang::get('admin_messages.category'),
            'is_recommended' => Lang::get('admin_messages.is_recommended'),
            'status' => Lang::get('admin_messages.status'),
        );

        $locales = array_keys($request_data->translations ?? []);
        $translation_rules = $this->getTranslationValidation($locales);

        $rules = array_merge($rules,$translation_rules['rules']);
        $attributes = array_merge($attributes,$translation_rules['attributes']);

        $this->validate($request_data,$rules,[],$attributes);
    }
}
