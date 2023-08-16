<?php

/**
 * Blog Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    BlogController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\BlogsDataTable;
use App\Models\Blog;
use App\Traits\Translatable;
use Lang;

class BlogController extends Controller
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
        $this->view_data['active_menu'] = 'blogs';
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_blogs');
        $this->view_data['sub_title'] = Lang::get('admin_messages.blogs');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BlogsDataTable $dataTable)
    {
        return $dataTable->render('admin.blogs.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_blog');
        $this->view_data['result'] = $result = new Blog;
        $this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
        $this->view_data['categories'] = resolve('BlogCategory')->where('status','1')->pluck('title','id');
        return view('admin.blogs.add', $this->view_data);
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

        $blog = new Blog;
        $blog->setLocale(global_settings('default_language'));
        $blog->title = $request->title;
        $blog->slug = $request->slug ?? \Str::slug($request->title);
        $blog->content = $request->content;
        $blog->category_id = $request->category;
        $blog->is_popular = $request->is_popular;
        $blog->status = $request->status;
        $blog->save();

        $this->updateTranslation($blog,$request->translations ?? []);
        
        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
        return redirect()->route('admin.blogs');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_blog');
        $this->view_data['result'] = $result = Blog::findOrFail($id);
        $this->view_data['translations'] = $this->getTranslationsExceptDefault($result);
        $this->view_data['categories'] = resolve('BlogCategory')->pluck('title','id');
        return view('admin.blogs.edit', $this->view_data);
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

        $blog = Blog::findOrFail($id);
        $blog->setLocale(global_settings('default_language'));
        $blog->title = $request->title;
        $blog->slug = $request->slug ?? \Str::slug($request->title);
        $blog->content = $request->content;
        $blog->category_id = $request->category;
        $blog->is_popular = $request->is_popular;
        $blog->status = $request->status;
        $blog->save();
        
        $this->deleteTranslations($blog,$request->removed_translations);
        $this->updateTranslation($blog,$request->translations ?? []);
        
        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return redirect()->route('admin.blogs');
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
            Blog::where('id',$id)->delete();
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy["status_message"]);
        }
        return redirect()->route('admin.blogs');
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
            'is_popular' => 'required',
            'status'    => 'required',
        );

        $attributes = array(
            'title' => Lang::get('admin_messages.title'),
            'content' => Lang::get('admin_messages.content'),
            'category' => Lang::get('admin_messages.category'),
            'is_popular' => Lang::get('admin_messages.is_popular'),
            'status' => Lang::get('admin_messages.status'),
        );

        $locales = array_keys($request_data->translations ?? []);
        $translation_rules = $this->getTranslationValidation($locales);

        $rules = array_merge($rules,$translation_rules['rules']);
        $attributes = array_merge($attributes,$translation_rules['attributes']);

        $this->validate($request_data,$rules,[],$attributes);
    }
}
