<?php

/**
 * Blog Category Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    BlogCategoryController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\BlogCategoriesDataTable;
use App\Models\BlogCategory;
use App\Models\Blog;
use App\Traits\Translatable;
use Lang;

class BlogCategoryController extends Controller
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
        $this->view_data['main_title']  = Lang::get('admin_messages.manage_blog_categories');
        $this->view_data['sub_title'] = Lang::get('admin_messages.blog_categories');
        $this->view_data['active_menu'] = 'blog_categories';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BlogCategoriesDataTable $dataTable)
    {
        return $dataTable->render('admin.blog_categories.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_blog_category');
        $this->view_data['result'] = $result = new BlogCategory;
        $this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
        return view('admin.blog_categories.add', $this->view_data);
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

        $blog_category = new BlogCategory;
        $blog_category->setLocale(global_settings('default_language'));
        $blog_category->title = $request->title;
        $blog_category->slug = $request->slug ?? \Str::slug($request->title);
        $blog_category->description = $request->description ?? '';
        $blog_category->is_popular = $request->is_popular;
        $blog_category->status = $request->status;

        if($request->file('image')) {
            $upload_result = $this->uploadImage($request->file('image'),$blog_category->filePath);
            if(!$upload_result['status']) {
                flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
                return redirect()->route('admin.blog_categories');
            }
            $blog_category->image = $upload_result['file_name'];
            $blog_category->upload_driver = $upload_result['upload_driver'];
        }

        $blog_category->save();

        $this->updateTranslation($blog_category,$request->translations ?? []);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
        return redirect()->route('admin.blog_categories');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_blog_category');
        $this->view_data['result'] = $result = BlogCategory::findOrFail($id);
        $this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
        return view('admin.blog_categories.edit', $this->view_data);
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

        $blog_category = BlogCategory::Find($id);
        $blog_category->setLocale(global_settings('default_language'));
        $blog_category->title = $request->title;
        $blog_category->slug = $request->slug ?? \Str::slug($request->title);
        $blog_category->description = $request->description ?? '';
        $blog_category->is_popular = $request->is_popular;
        $blog_category->status = $request->status;

        if($request->file('image')) {
            $blog_category->deleteImageFile();

            $upload_result = $this->uploadImage($request->file('image'),$blog_category->filePath);
            if(!$upload_result['status']) {
                flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
                return redirect()->route('admin.blog_categories');
            }
            $blog_category->image = $upload_result['file_name'];
            $blog_category->upload_driver = $upload_result['upload_driver'];
        }

        $blog_category->save();

        $this->deleteTranslations($blog_category,$request->removed_translations);
        $this->updateTranslation($blog_category,$request->translations ?? []);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.blog_categories');
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
                $blog_category = BlogCategory::find($id);
                $blog_category->deleteImageFile();
                $blog_category->delete();

                flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
            }
            catch (\Exception $e) {
                flashMessage('danger',Lang::get('admin_messages.failed'),$e->getMessage());
            }
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy['status_message']);
        }
        return redirect()->route('admin.blog_categories');
    }

    /**
     * Upload Given Image to Server
     *
     * @return Object Upload Result
     */
    protected function uploadImage($image,$target_dir)
    {
        $image_handler = resolve('App\Contracts\ImageHandleInterface');

        $image_data['name_prefix'] = 'blog_category_';
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
        $blog_category_count = BlogCategory::where('category_id',$id)->count();
        if($blog_category_count > 0) {
            return ['status' => false,'status_message' => Lang::get('admin_messages.this_blog_category_has_child_category')];
        }
        $blog_count = Blog::where('category_id',$id)->count();
        if($blog_count > 0) {
            return ['status' => false,'status_message' => Lang::get('admin_messages.this_blog_category_has_some_blogs')];
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
            'is_popular'=> 'required',
            'status'    => 'required',
        );

        $attributes = array(
            'title'         => Lang::get('admin_messages.title'),
            'description'   => Lang::get('admin_messages.description'),
            'image'         => Lang::get('admin_messages.image'),
            'is_popular'    => Lang::get('admin_messages.is_popular'),
            'status'        => Lang::get('admin_messages.status'),
        );

        $locales = array_keys($request_data->translations ?? []);
        $translation_rules = $this->getTranslationValidation($locales);

        $rules = array_merge($rules,$translation_rules['rules']);
        $attributes = array_merge($attributes,$translation_rules['attributes']);

        $this->validate($request_data,$rules,[],$attributes);
    }
}
