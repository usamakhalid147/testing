<?php

/**
 * Blogs Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    Blogs
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Blog;
use Lang;

class BlogsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
        ->addColumn('title', function($query) {
            return $query->title;
        })
        ->addColumn('category', function($query) {
            return $this->getCategoryName($query->category_id);
        })
        ->addColumn('is_popular', function($query) {
            return getYesNoText($query->is_popular);
        })
        ->addColumn('status', function($query) {
            return getStatusText($query->status);
        })
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-blogs') ? '<a href="'.route('admin.blogs.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-blogs') ? '<a href="" data-action="'.route('admin.blogs.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Blog $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Blog $model)
    {
        $locale = global_settings('default_language');
        $query = $model->join('blog_categories', function($join) {
            $join->on('blog_categories.id', '=', 'blogs.category_id');
        })
        ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(blog_categories.title, \'$.'.$locale.'\')) as blog_category, JSON_UNQUOTE(JSON_EXTRACT(blogs.title, \'$.'.$locale.'\')) as blog_title, blogs.id as id, blogs.is_popular as is_popular, blogs.status as status');
        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->addAction()
                    ->orderBy(0)
                    ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'id', 'name' => 'id', 'title' => Lang::get('admin_messages.id')],
            ['data' => 'blog_title', 'name' => 'blogs.title', 'title' => Lang::get('admin_messages.title')],
            ['data' => 'blog_category', 'name' => 'blog_categories.title', 'title' => Lang::get('admin_messages.category')],
            ['data' => 'is_popular', 'name' => 'is_popular', 'title' => Lang::get('admin_messages.is_popular')],
            ['data' => 'status', 'name' => 'status', 'title' => Lang::get('admin_messages.status')],
        ];
    }

    /**
     * Get builder parameters.
     *
     * @return array
     */
    protected function getBuilderParameters()
    {
        return array(
            'dom' => config('datatables-buttons.parameters.dom'),
            'buttons' => config('datatables-buttons.parameters.buttons'),
        );
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'blog_' . date('YmdHis');
    }

    /**
     * Get Category Name
     *
     * @return string
     */
    protected function getCategoryName($id)
    {
        $category = resolve('BlogCategory')->where('id',$id)->first();
        return optional($category)->title ?? '';
    }
}