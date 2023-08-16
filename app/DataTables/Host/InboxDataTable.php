<?php

/**
 * Inbox Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables\Host
 * @category    InboxDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables\Host;

use Yajra\DataTables\Services\DataTable;
use App\Models\Message;
use Lang;

class InboxDataTable extends DataTable
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
            $reservation = $query->list_type.'_reservation';
            return '<a href="'.route('host.reservations.show',['id' => $query->reservation_id]).'">'.$query->$reservation->code.'</a>';
        })
        ->addColumn('action',function($query) {
            $view ='<a href="'.route('host.messages.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-comment-dots"></i> </a>';
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
    public function query(Message $model)
    {

        $model = $model
            ->where(function($query) {
                $query->where(function($query) {
                    $query->where('list_type','hotel')->whereHas('hotel_reservation', function($query) {
                        $query->where('host_id',\Auth::id());
                    });
                });
                /*ExperienceCommentStart*/
                // $query->orWhere(function($query) {
                //  $query->where('list_type','experience')->whereHas('experience_reservation', function($query) {
                //      $query->where($reserve_user_column,Auth::id());
                //  });
                // });
                /*ExperienceCommentEnd*/
            })
            ->orderByDesc('updated_at');

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
            ['data' => 'reservation_code', 'name' => 'reservation_code', 'title' => Lang::get('admin_messages.code')],
            ['data' => 'user_name', 'name' => 'user_name', 'title' => Lang::get('admin_messages.guest_name')],
            ['data' => 'hotel_name', 'name' => 'hotel_name', 'title' => Lang::get('admin_messages.hotel_name')],
            ['data' => 'guest_message', 'name' => 'guest_message', 'title' => Lang::get('admin_messages.comments')],
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