<?php

/**
 * Discount Banners DataTable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    DiscountBannersDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\DiscountBanner;
use Lang;

class DiscountBannersDataTable extends DataTable
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
        ->addColumn('image', function ($query) {
            return '<img class="dt-thumb-image" src="'.$query->image_src.'">';
        })
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-discount_banners') ? '<a href="'.route('admin.discount_banners.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-discount_banners') ? '<a href="" data-action="'.route('admin.discount_banners.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        })
        ->rawColumns(['image','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param DiscountBanner $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DiscountBanner $model)
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
            ['data' => 'order_id', 'name' => 'order_id', 'title' => Lang::get('admin_messages.order_id')],
            ['data' => 'image', 'name' => 'image', 'title' => Lang::get('admin_messages.image'),'searchable' => false],
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
        return 'discount_banners_' . date('YmdHis');
    }
}