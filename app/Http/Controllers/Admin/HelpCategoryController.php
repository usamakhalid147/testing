<?php

/**
 * Help Category Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    HelpCategoryController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\HelpCategoriesDataTable;
use App\Models\HelpCategory;
use App\Models\Help;
use App\Traits\Translatable;
use Lang;

class HelpCategoryController extends Controller
{
    use Translatable;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['translatable_fields'] = $this->translatable_fields = collect([
            ['key' => 'title', 'title' => Lang::get('admin_messages.title'),'type' => 'text','rules' => 'required|max:50'],
            ['key' => 'description', 'title' => Lang::get('admin_messages.description'),'type' => 'textarea'],
        ]);
        $this->view_data['main_title']  = Lang::get('admin_messages.manage_help_categories');
        $this->view_data['sub_title'] = Lang::get('admin_messages.help_categories');
        $this->view_data['active_menu'] = 'help_categories';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HelpCategoriesDataTable $dataTable)
    {
        return $dataTable->render('admin.help_categories.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_help_category');
        $this->view_data['result'] = $result = new HelpCategory;
        $this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
        return view('admin.help_categories.add', $this->view_data);
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

        $help_category = new HelpCategory;
        $help_category->setLocale(global_settings('default_language'));
        $help_category->title = $request->title;
        $help_category->slug = $request->slug ?? \Str::slug($request->title);
        $help_category->description = $request->description ?? '';
        $help_category->status = $request->status;

        if($request->file('image')) {
            $upload_result = $this->uploadImage($request->file('image'),$help_category->filePath);
            if(!$upload_result['status']) {
                flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
                return redirect()->route('admin.help_categories');
            }
            $help_category->image = $upload_result['file_name'];
            $help_category->upload_driver = $upload_result['upload_driver'];
        }

        $help_category->save();

        $this->updateTranslation($help_category,$request->translations ?? []);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
        return redirect()->route('admin.help_categories');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_help_category');
        $this->view_data['result'] = $result = HelpCategory::findOrFail($id);
        $this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
        return view('admin.help_categories.edit', $this->view_data);
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

        $help_category = HelpCategory::Find($id);
        $help_category->setLocale(global_settings('default_language'));
        $help_category->title = $request->title;
        $help_category->slug = $request->slug ?? \Str::slug($request->title);
        $help_category->description = $request->description ?? '';
        $help_category->status = $request->status;

        if($request->file('image')) {
            $help_category->deleteImageFile();

            $upload_result = $this->uploadImage($request->file('image'),$help_category->filePath);
            if(!$upload_result['status']) {
                flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
                return redirect()->route('admin.help_categories');
            }
            $help_category->image = $upload_result['file_name'];
            $help_category->upload_driver = $upload_result['upload_driver'];
        }

        $help_category->save();

        $this->deleteTranslations($help_category,$request->removed_translations);
        $this->updateTranslation($help_category,$request->translations ?? []);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.help_categories');
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
            try {
                $help_category = HelpCategory::find($id);
                $help_category->deleteImageFile();
                $help_category->delete();

                flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
            }
            catch (\Exception $e) {
                flashMessage('danger',Lang::get('admin_messages.failed'),$e->getMessage());
            }
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy['status_message']);
        }
        return redirect()->route('admin.help_categories');
    }

    /**
     * Upload Given Image to Server
     *
     * @return Object Upload Result
     */
    protected function uploadImage($image,$target_dir)
    {
        $image_handler = resolve('App\Contracts\ImageHandleInterface');

        $image_data['name_prefix'] = 'help_category_';
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
        $help_category_count = HelpCategory::where('category_id',$id)->count();
        if($help_category_count > 0) {
            return ['status' => false,'status_message' => Lang::get('admin_messages.this_help_category_has_child_category')];
        }
        $help_count = Help::where('category_id',$id)->count();
        if($help_count > 0) {
            return ['status' => false,'status_message' => Lang::get('admin_messages.this_help_category_has_some_helps')];
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
            'title'     => 'required|max:50',
            'image'     => 'mimes:'.view()->shared('valid_mimes'),
            'status'    => 'required',
        );

        $attributes = array(
            'title'         => Lang::get('admin_messages.title'),
            'description'   => Lang::get('admin_messages.description'),
            'image'         => Lang::get('admin_messages.image'),
            'status'        => Lang::get('admin_messages.status'),
        );

        $locales = array_keys($request_data->translations ?? []);
        $translation_rules = $this->getTranslationValidation($locales);

        $rules = array_merge($rules,$translation_rules['rules']);
        $attributes = array_merge($attributes,$translation_rules['attributes']);

        $this->validate($request_data,$rules,[],$attributes);
    }
}
