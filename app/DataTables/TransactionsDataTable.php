<?php

/**
 * Transactions Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    TransactionsDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Transaction;
use Lang;

class TransactionsDataTable extends DataTable
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
        ->addColumn('list_type', function ($query) {
            return Lang::get('messages.'.$query->list_type);
        })
        ->addColumn('type', function ($query) {
            return getCurrentUser()->can('view-reservations') ? '<a href="'.route('admin.reservations.show',['id' => $query->reservation_id]).'" class=""> '.Lang::get('messages.'.$query->type).' </a>' : Lang::get('messages.'.$query->type);            
        })
        ->addColumn('user_name', function ($query) {
            return $query->user->first_name;
        })
        ->addColumn('amount', function ($query) {
            return $query->currency_symbol.$query->amount;
        })
        ->addColumn('created_at', function ($query) {
            return $query->created_at->format(DATE_FORMAT.' '.TIME_FORMAT);
        })
        ->rawColumns(['type']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param Transaction $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transaction $model)
    {
        return $model->with('user')->select();
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
            ['data' => 'list_type', 'name' => 'list_type', 'title' => Lang::get('admin_messages.list_type')],
            ['data' => 'type', 'name' => 'type', 'title' => Lang::get('admin_messages.type')],
            ['data' => 'payment_method', 'name' => 'payment_method', 'title' => Lang::get('admin_messages.payment_method')],
            ['data' => 'transaction_id', 'name' => 'transaction_id', 'title' => Lang::get('admin_messages.transaction_id')],
            ['data' => 'user_name', 'name' => 'user_name', 'title' => Lang::get('admin_messages.user_name')],
            ['data' => 'amount', 'name' => 'amount', 'title' => Lang::get('admin_messages.amount')],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => Lang::get('admin_messages.created_at')],
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
        return 'transactions_' . date('YmdHis');
    }
}