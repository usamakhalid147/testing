<?php

/**
 * User Penalties Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    PenaltiesDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\UserPenalty;
use Lang;

class PenaltiesDataTable extends DataTable
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
        ->addColumn('total',function($query) {
            return $query->currency->symbol.' '.$query->total;
        })
        ->addColumn('paid',function($query) {
            return $query->currency->symbol.' '.$query->paid;
        })
        ->addColumn('remaining',function($query) {
            return $query->currency->symbol.' '.$query->remaining;
        })
        ->addColumn('action',function($query) {
            $view = getCurrentUser()->can('manage-penalties') ? '<a href="'.route('admin.penalties.show',['user_penalty' => $query]).'" class="h3"> <i class="fa fa-eye"></i> </a>' : '';
            return $view;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\UserPenalty $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(UserPenalty $model)
    {
        return $model->join('users', function($join) {
                $join->on('users.id', '=', 'user_penalties.user_id');
            })
            ->join('currencies', function($join) {
                $join->on('currencies.code', '=', 'user_penalties.currency_code');
            })
            ->select(['users.first_name as host_name', 'user_penalties.*']);
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
                    ->addAction(['exportable' => false])
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
            ['data' => 'id', 'name' => 'user_penalties.id', 'title' => Lang::get('admin_messages.id')],
            ['data' => 'host_name', 'name' => 'users.first_name', 'title' => Lang::get('admin_messages.manager_name')],
            ['data' => 'total', 'name' => 'user_penalties.total', 'title' => Lang::get('admin_messages.total').' '.Lang::get('admin_messages.amount')],
            ['data' => 'paid', 'name' => 'user_penalties.paid', 'title' => Lang::get('admin_messages.amount').' '.Lang::get('admin_messages.paid')],
            ['data' => 'remaining', 'name' => 'user_penalties.remaining', 'title' => Lang::get('admin_messages.amount').' '.Lang::get('admin_messages.remaining')],
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
        return 'penalties_' . date('YmdHis');
    }
}