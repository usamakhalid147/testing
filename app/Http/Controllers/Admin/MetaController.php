<?php

/**
 * Meta Information Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    MetaController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\MetasDataTable;
use App\Models\Meta;
use App\Traits\Translatable;
use Lang;

class MetaController extends Controller
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
            ['key' => 'description', 'title' => Lang::get('admin_messages.description'),'type' => 'textarea'],
        ]);
        $this->view_data['main_title']  = Lang::get('admin_messages.manage_metas');
        $this->view_data['sub_title'] = Lang::get('admin_messages.metas');
        $this->view_data['active_menu'] = 'metas';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(MetasDataTable $dataTable)
    {
        return $dataTable->render('admin.metas.view',$this->view_data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_meta');
        $this->view_data['result'] = $result = resolve("Meta")->where('id',$id)->first();
        $this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
        return view('admin.metas.edit', $this->view_data);
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

        $meta = Meta::findOrFail($id);
        $meta->setLocale(global_settings('default_language'));
        $meta->title = $request->title;
        $meta->description = $request->description;
        $meta->keywords = $request->keywords;
        $meta->save();

        $this->deleteTranslations($meta,$request->removed_translations);
        $this->updateTranslation($meta,$request->translations ?? []);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.metas');
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
            'title' => 'required|max:100',
        );

        $attributes = array(
            'title' => Lang::get('admin_messages.title'),
        );

        $locales = array_keys($request_data->translations ?? []);
        $translation_rules = $this->getTranslationValidation($locales);

        $rules = array_merge($rules,$translation_rules['rules']);
        $attributes = array_merge($attributes,$translation_rules['attributes']);

        $this->validate($request_data,$rules,[],$attributes);
    }
}