<?php

/**
 * Reviews Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    ReviewsDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

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
        ->addColumn('review_by',function($query) {
            return Lang::get('admin_messages.'.$query->review_by);
        })
        ->addColumn('recommend',function($query) {
            return getYesNoText($query->recommend);
        })
        ->addColumn('action',function($query) {
            $view = getCurrentUser()->can('update-reviews') ? '<a href="'.route('admin.reservations.show',['id' => $query->reservation_id]).'" class="h3"> <i class="fa fa-eye"></i> </a>' : '';
            $edit = getCurrentUser()->can('update-reviews') ? '<a href="'.route('admin.reviews.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            return $edit."&nbsp;".$view;
        })
        ->rawColumns(['reservation_id','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Review $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Review $model)
    {
        $locale = global_settings('default_language');
        return $model
            ->join('hotels', function($join) {
                $join->on('hotels.id', '=', 'reviews.hotel_id');
            })
            ->join('users', function($join) {
                $join->on('users.id', '=', 'reviews.user_from');
            })
            ->join('users as to_user', function($join) {
                $join->on('to_user.id', '=', 'reviews.user_to');
            })
            ->selectRaw('users.first_name as user_name, to_user.first_name as to_user_name, JSON_UNQUOTE(JSON_EXTRACT(hotels.name, \'$.'.$locale.'\')) as hotel_name, reviews.reservation_id as reservation_id, reviews.id as id, reviews.review_by as review_by,reviews.recommend as recommend, reviews.*');
        return $model;
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
            ['data' => 'id', 'name' => 'id', 'title' => Lang::get('admin_messages.id')],
            ['data' => 'reservation_id', 'name' => 'reservation_id', 'title' => Lang::get('admin_messages.reservation_id')],
            ['data' => 'hotel_name', 'name' => 'hotels.name', 'title' => Lang::get('admin_messages.hotel_name')],
            ['data' => 'user_name', 'name' => 'users.first_name', 'title' => Lang::get('admin_messages.user_name')],
            ['data' => 'to_user_name', 'name' => 'to_user.first_name', 'title' => Lang::get('admin_messages.user_to')],
            ['data' => 'review_by', 'name' => 'review_by', 'title' => Lang::get('admin_messages.review_by')],
            ['data' => 'rating', 'name' => 'rating', 'title' => Lang::get('admin_messages.review').' '.Lang::get('messages.rating')],
            ['data' => 'recommend', 'name' => 'recommend', 'title' => Lang::get('messages.recommend')],
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