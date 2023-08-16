<?php

/**
 * Admin Users Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    AdminUsersDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Admin;
use Lang;

class AdminUsersDataTable extends DataTable
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
        ->addColumn('primary', function($query) {
            $primary = $query->primary ? 'yes' : 'no';
            return \Lang::get('admin_messages.'.$primary);
        })
        ->addColumn('action',function($query) {
            $edit = '<a href="'.route('admin.admin_users.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>';
            $delete = '<a href="" data-action="'.route('admin.admin_users.delete',['id' => $query->id]).'" class="h3" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"> <i class="fa fa-trash-alt"></i> </a>';
            return $edit." &nbsp; ".$delete;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Admin $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Admin $model)
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
            ['data' => 'username', 'name' => 'username', 'title' => Lang::get('admin_messages.agent')." ".Lang::get('admin_messages.name')],
            ['data' => 'email', 'name' => 'email', 'title' => Lang::get('admin_messages.agent')." ".Lang::get('admin_messages.email')],
            ['data' => 'primary', 'name' => 'primary', 'title' => Lang::get('admin_messages.primary')],
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
        return 'admin_users_' . date('YmdHis');
    }
}