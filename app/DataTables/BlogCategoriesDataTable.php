<?php

/**
 * Blog Categories Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    BlogCategories
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\BlogCategory;
use Lang;

class BlogCategoriesDataTable extends DataTable
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
        ->addColumn('is_popular', function($query) {
            return getYesNoText($query->is_popular);
        })
        ->addColumn('status', function($query) {
            return getStatusText($query->status);
        })
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-blog_categories') ? '<a href="'.route('admin.blog_categories.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-blog_categories') ? '<a href="" data-action="'.route('admin.blog_categories.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param BlogCategory $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(BlogCategory $model)
    {
        $locale = global_settings('default_language');
        $query = $model->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(blog_categories.title, \'$.'.$locale.'\')) as blog_category_title, JSON_UNQUOTE(JSON_EXTRACT(blog_categories.description, \'$.'.$locale.'\')) as blog_category_description, blog_categories.id as id, blog_categories.is_popular as is_popular, blog_categories.status as status');
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
            ['data' => 'blog_category_title', 'name' => 'blog_categories.title', 'title' => Lang::get('admin_messages.title')],
            ['data' => 'blog_category_description', 'name' => 'blog_categories.description', 'title' => Lang::get('admin_messages.description')],
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
        return 'blog_category_' . date('YmdHis');
    }
}