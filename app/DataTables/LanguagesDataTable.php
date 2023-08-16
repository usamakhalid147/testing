<?php

/**
 * Languages Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    LanguagesDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Language;
use Lang;

class LanguagesDataTable extends DataTable
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
        ->addColumn('status', function($query) {
            return getStatusText($query->status);
        })
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-languages') ? '<a href="'.route('admin.languages.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-languages') ? '<a href="" data-action="'.route('admin.languages.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Language $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Language $model)
    {
        return $model->select();
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
            ['data' => 'code', 'name' => 'code', 'title' => Lang::get('admin_messages.code')],
            ['data' => 'name', 'name' => 'name', 'title' => Lang::get('admin_messages.name')],
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
        return 'languages_' . date('YmdHis');
    }
}