<?php

/**
 * Helps Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    Helps
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Help;
use Lang;

class HelpsDataTable extends DataTable
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
        ->addColumn('is_recommended', function($query) {
            return getYesNoText($query->is_recommended);
        })
        ->addColumn('status', function($query) {
            return getStatusText($query->status);
        })
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-helps') ? '<a href="'.route('admin.helps.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-helps') ? '<a href="" data-action="'.route('admin.helps.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Help $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Help $model)
    {
        $locale = global_settings('default_language');
        $query = $model->join('help_categories', function($join) {
            $join->on('help_categories.id', '=', 'helps.category_id');
        })
        ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(help_categories.title, \'$.'.$locale.'\')) as help_category, JSON_UNQUOTE(JSON_EXTRACT(helps.title, \'$.'.$locale.'\')) as help_title, helps.id as id, helps.is_recommended as is_recommended, helps.status as status');
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
            ['data' => 'help_title', 'name' => 'helps.title', 'title' => Lang::get('admin_messages.title')],
            ['data' => 'help_category', 'name' => 'help_categories.title', 'title' => Lang::get('admin_messages.category')],
            ['data' => 'is_recommended', 'name' => 'is_recommended', 'title' => Lang::get('admin_messages.is_recommended')],
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
        return 'help_' . date('YmdHis');
    }

    /**
     * Get Category Name
     *
     * @return string
     */
    protected function getCategoryName($id)
    {
        $category = resolve('HelpCategory')->where('id',$id)->first();
        return optional($category)->title ?? '';
    }
}