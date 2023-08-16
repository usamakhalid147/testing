<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use Lang;

class TranslationController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.translations');
        $this->view_data['sub_title'] = Lang::get('admin_messages.translations');
        $this->view_data['active_menu'] = 'translations';
        $this->translation_service = resolve('App\Services\TranslationService');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data['file'] = $request->file ?? 'admin_messages';
        $data['locale'] = $request->language ?? global_settings('default_language');
        $data['search_text'] = $request->search_text ?? '';
        $data['is_select_all'] = false;
        $translation_data = $this->translation_service->scan($data);
        $this->view_data['translation_data'] = $translation_data['translations'];
        $this->view_data['translation_result'] = $translation_data['translation_result'];
        if($request->isMethod("POST")) {
            return response()->json([
                'status' => true,
                'data' => $this->view_data,
            ]);
        }
        return view('admin.translations.view',$this->view_data);
    }

    public function update(Request $request)
    {
        $translation_service = $this->translation_service;
        $data = [];
        $data['file'] = $request->file ?? 'admin_messages';
        $data['locale'] = $request->language ?? global_settings('default_language');
        $data['search_text'] = $search_text = $request->search_text ?? '';
        $data['is_select_all'] = true;
        $translation_result = $request->translation_result;
        $translation_data = $this->translation_service->scan($data);
        $translations = $translation_data['translations'];
        $trans_result = [];
        $translations = $translations->map(function ($trans,$path) use($translation_result,$search_text,$translation_service) {
             $trans = $trans->map(function ($tran,$key) use($translation_result,$search_text,$translation_service,$path) {
                    $file = app()->make('path.lang').'/'.$key.'/'.$path.'.php';
                    $tran[$search_text] = $translation_result[$key];
                    $translation_service->updateTranslation($file, $tran);
                    return $tran;
                });
            return $trans;
        });
        
        flashMessage('success',Lang::get('messages.success'),Lang::get('admin_messages.details_updated'));

        return redirect()->route('admin.translations');
    }
}
