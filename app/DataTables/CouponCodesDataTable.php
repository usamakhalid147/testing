<?php

/**
 * Coupon Codes Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    CouponCodesDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\CouponCode;
use Lang;

class CouponCodesDataTable extends DataTable
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
        ->addColumn('min_amount', function($query) {
            return $query->currency_symbol.''.$query->min_amount;
        })
        ->addColumn('amount', function($query) {
            return $query->currency_symbol.''.$query->value;
        })
        ->addColumn('start_date', function($query) {
            return $query->start_date->format(DATE_FORMAT);
        })
        ->addColumn('end_date', function($query) {
            return $query->end_date->format(DATE_FORMAT);
        })
        ->addColumn('visible_on_public', function($query) {
            return getYesNoText($query->visible_on_public);
        })
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-coupon_codes') ? '<a href="'.route('admin.coupon_codes.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-coupon_codes') ? '<a href="" data-action="'.route('admin.coupon_codes.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param CouponCode $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(CouponCode $model)
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
            ['data' => 'code', 'name' => 'code', 'title' => Lang::get('admin_messages.discount_title')],
            ['data' => 'amount', 'name' => 'amount', 'title' => Lang::get('admin_messages.amount')],
            ['data' => 'min_amount', 'name' => 'min_amount', 'title' => Lang::get('admin_messages.min_amount')],
            ['data' => 'visible_on_public', 'name' => 'visible_on_public', 'title' => Lang::get('admin_messages.visible_on_public')],
            ['data' => 'start_date', 'name' => 'start_date', 'title' => Lang::get('admin_messages.start_date')],
            ['data' => 'end_date', 'name' => 'end_date', 'title' => Lang::get('admin_messages.end_date')],
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
        return 'coupon_codes_' . date('YmdHis');
    }
}