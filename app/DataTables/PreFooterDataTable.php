<?php

/**
 * Pre Footers Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    PreFootersDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\PreFooter;
use Lang;

class PreFooterDataTable extends DataTable
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
            $edit = getCurrentUser()->can('update-pre_footers') ? '<a href="'.route('admin.pre_footers.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-pre_footers') ? '<a href="" data-action="'.route('admin.pre_footers.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        })
        ->rawColumns(['image','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param CommunityBanner $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PreFooter $model)
    {
        $locale = global_settings('default_language');
        $query = $model->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(pre_footers.title, \'$.'.$locale.'\')) as pre_footer_title, JSON_UNQUOTE(JSON_EXTRACT(pre_footers.description, \'$.'.$locale.'\')) as pre_footer_description, pre_footers.id as id,pre_footers.image as image,pre_footers.upload_driver as upload_driver,pre_footers.status as status');
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
            ['data' => 'pre_footer_title', 'name' => 'pre_footers.title', 'title' => Lang::get('admin_messages.title')],
            ['data' => 'pre_footer_description', 'name' => 'pre_footers.description', 'title' => Lang::get('admin_messages.description')],
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
        return 'pre_footers_' . date('YmdHis');
    }
}