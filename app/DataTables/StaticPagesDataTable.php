<?php

/**
 * Static Pages Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    StaticPagesDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\StaticPage;
use Lang;

class StaticPagesDataTable extends DataTable
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
        ->addColumn('in_footer', function($query) {
            return getYesNoText($query->in_footer);
        })
        ->addColumn('action',function($query) {
            $preview = '<a href="'.route('static_page',['slug' => $query->slug]).'" target="_new" class="h3"> <i class="fa fa-eye"></i> </a>';
            $edit = '<a href="'.route('admin.static_pages.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>';
            $delete = '<a href="" data-action="'.route('admin.static_pages.delete',['id' => $query->id]).'" class="h3" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"> <i class="fa fa-trash-alt"></i> </a>';
            return $preview." &nbsp; ".$edit." &nbsp; ".$delete;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param StaticPage $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(StaticPage $model)
    {
        $locale = global_settings('default_language');
        $query = $model->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(static_pages.name, \'$.'.$locale.'\')) as static_page_name, static_pages.id as id, static_pages.in_footer as in_footer, static_pages.slug as slug, static_pages.status as status');
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
            ['data' => 'static_page_name', 'name' => 'static_pages.name', 'title' => Lang::get('admin_messages.title')],
            ['data' => 'in_footer', 'name' => 'in_footer', 'title' => Lang::get('admin_messages.footer')],
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
        return 'pages_' . date('YmdHis');
    }
}