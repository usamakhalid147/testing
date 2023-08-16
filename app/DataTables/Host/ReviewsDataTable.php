<?php

/**
 * Reviews Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables\Host
 * @category    ReviewsDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables\Host;

use Yajra\DataTables\Services\DataTable;
use App\Models\Review;
use Lang;

class ReviewsDataTable extends DataTable
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
        ->addColumn('hotel_name',function($query) {
            return $query->hotel->name;
        })
        ->addColumn('user_name',function($query){
            return $query->user->first_name;
        })
        ->addColumn('reservation_code',function($query) {
            return '<a href="'.route('host.reservations.show',['id' => $query->reservation_id]).'">'.$query->reservation->code.'</a>';
        })
        ->addColumn('action',function($query) {
            $view ='<a href="'.route('host.reviews.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>';
            return $view;
        })
        ->rawColumns(['reservation_code','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Review $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Review $model)
    {
        return $model->authUser()->orderBy('public_reply')->get();
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
            ['data' => 'reservation_code', 'name' => 'reservation_code', 'title' => Lang::get('admin_messages.code')],
            ['data' => 'user_name', 'name' => 'user_name', 'title' => Lang::get('admin_messages.guest_name')],
            ['data' => 'hotel_name', 'name' => 'hotel_name', 'title' => Lang::get('admin_messages.hotel_name')],
            ['data' => 'rating', 'name' => 'rating', 'title' => Lang::get('admin_messages.review')." ".Lang::get('messages.rating')],
            ['data' => 'public_comment', 'name' => 'reviews.public_comment', 'title' => Lang::get('messages.review_about_property')],
            ['data' => 'public_reply', 'name' => 'reviews.public_reply', 'title' => Lang::get('messages.your_response')],
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
        return 'reviews_' . date('YmdHis');
    }
}