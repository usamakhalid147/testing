<?php

/**
 * Metas Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    MetasDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Meta;
use Lang;

class MetasDataTable extends DataTable
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
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-metas') ? '<a href="'.route('admin.metas.edit',['id' => $query->id]).'" class=""> <i class="fa fa-edit"></i> </a>' : '';
            return $edit;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Meta $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Meta $model)
    {
        $locale = global_settings('default_language');
        $query = $model->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(metas.title, \'$.'.$locale.'\')) as meta_title, metas.id as id, metas.display_name as display_name, metas.keywords as keywords');
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
            ['data' => 'display_name', 'name' => 'display_name', 'title' => Lang::get('admin_messages.name')],
            ['data' => 'meta_title', 'name' => 'metas.title', 'title' => Lang::get('admin_messages.title')],
            ['data' => 'keywords', 'name' => 'keywords', 'title' => Lang::get('admin_messages.keywords')],
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
        return 'metas_' . date('YmdHis');
    }
}