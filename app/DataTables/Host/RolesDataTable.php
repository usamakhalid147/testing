<?php

/**
 * Roles Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    RolesDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables\Host;

use Yajra\DataTables\Services\DataTable;
use App\Models\Role;
use Lang;

class RolesDataTable extends DataTable
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
        ->addColumn('action',function($role) {
            $edit = getCurrentUser()->can('update-host_roles') ? '<a href="'.route('host.roles.edit',['id' => $role->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-host_roles') ? '<a href="#" class="h3" data-action="'.route('host.roles.delete',['id' => $role->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Role $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Role $model)
    {
        return $model->where('user_type','host')->where('user_id',getCurrentUserId())->select();
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
            ['data' => 'display_name', 'name' => 'display_name', 'title' => Lang::get('admin_messages.name')],
            ['data' => 'description', 'name' => 'description', 'title' => Lang::get('admin_messages.role_title')],
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
        return 'roles_' . date('YmdHis');
    }
}