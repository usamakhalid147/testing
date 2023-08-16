<?php

/**
 * Host Coupon Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables\Host
 * @category    HostCouponCodesDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables\Host;

use Yajra\DataTables\Services\DataTable;
use App\Models\HostCouponCode;
use Lang;

class HostCouponCodesDataTable extends DataTable
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
        ->addColumn('coupon_value', function($query) {
            return $query->coupon_value;
        })
        ->addColumn('display_user', function($query) {
            return getYesNoText($query->visible_on_public);
        })
        ->addColumn('min_amount', function($query) {
            return $query->currency_symbol.$query->min_amount;
        })
        ->addColumn('start_date', function($query) {
            return getDateInFormat($query->start_date);
        })
        ->addColumn('end_date', function($query) {
            return getDateInFormat($query->end_date);
        })
        ->addColumn('action',function($query) {
            $edit = '<a href="'.route('host.coupon_codes.edit',['id' => $query->id]).'" class="h3 info me-2"> <i class="fa fa-edit"></i> </a>';
            $delete = '<a href="" data-action="'.route('host.coupon_codes.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3 info"> <i class="fa fa-trash-alt"></i> </a>';
            return $edit."".$delete;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param HostCouponCode $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(HostCouponCode $model)
    {
        return $model->authUser()->get();
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
            ['data' => 'code', 'name' => 'code', 'title' => Lang::get('admin_messages.promotion_title')],
            ['data' => 'coupon_value', 'name' => 'coupon_value', 'title' => Lang::get('admin_messages.coupon_value')],
            ['data' => 'display_user', 'name' => 'display_user', 'title' => Lang::get('admin_messages.display_user')],
            ['data' => 'min_amount', 'name' => 'min_amount', 'title' => Lang::get('admin_messages.min_amount')],
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