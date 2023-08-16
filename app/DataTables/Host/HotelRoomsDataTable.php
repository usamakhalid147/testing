<?php

/**
 * Hotels Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    HotelRoomsDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables\Host;

use Yajra\DataTables\Services\DataTable;
use App\Models\HotelRoom;
use Lang;

class HotelRoomsDataTable extends DataTable
{

    protected $hotel_id;

    /**
     * Set the value for Type
     *
     */
    public function setHotel($hotel_id)
    {
        $this->hotel_id = $hotel_id;
        return $this;
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
        ->addColumn('room_name',function($query) {
            return '<a href="'.resolveRoute('hotel_details',['id' => $query->hotel_id]).'" target="_blank"> '.$query->name.' </a>';
        })
        ->addColumn('price',function($query){
            return $query->hotel_room_price->currency_symbol.$query->hotel_room_price->price;
        })
        ->addColumn('listed', function ($query) {
            if($query->status == 'In Progress') {
                return Lang::get('admin_messages.in_progress');
            }
            if(!in_array($query->status,['Listed','Unlisted'])) {
                return $query->status;
            }
            $options = array(
                'Listed'   => Lang::get('admin_messages.listed'),
                'Unlisted'  => Lang::get('admin_messages.unlisted'),
            );
            $listed =  '<select class="form-select datatable-select room-admin_status" data-id="'.$query->id.'">';
            foreach ($options as $key => $value) {
                $selected = ($query->status == $key) ? 'selected' : '';
                $listed .= '<option value="'.$key.'" '.$selected.'>'. $value .'</option>';
            }
            $listed .= '</select>';

            return $listed;
        })
        ->addColumn('action',function($query) {
            $edit = '<a class="me-2 h3" href="'.route('host.rooms.edit',['id' => $query->id]).'"> <i class="fa fa-edit"></i> </a>';
            $delete ='<a class="me-2 h3" href="" data-action="'.route('host.rooms.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"> <i class="fa fa-trash-alt"></i> </a>';
            return "<div class='d-flex'> &nbsp; ".$edit." &nbsp; ".$delete."</div>";
        })
        ->rawColumns(['room_name','host_name','action','listed']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param Hotel $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(HotelRoom $model)
    {
        $model = $model->authUser();
        if($this->hotel_id) {
            return $model->where('hotel_id',$this->hotel_id)->get();
        }
        return $model->get();
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
            ['data' => 'room_name', 'name' => 'room_name', 'title' => Lang::get('messages.room')." ".Lang::get('admin_messages.category')],
            ['data' => 'number', 'name' => 'number', 'title' => Lang::get('admin_messages.total_no_category_desc')],
            ['data' => 'adults', 'name' => 'adults', 'title' => Lang::get('admin_messages.base_adults')],
            ['data' => 'children', 'name' => 'children', 'title' => Lang::get('admin_messages.base_children')],
            ['data' => 'price', 'name' => 'price', 'title' => Lang::get('admin_messages.room_rate')],
            ['data' => 'listed', 'name' => 'listed', 'title' => Lang::get('admin_messages.status')],
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
        return 'rooms_' . date('YmdHis');
    }
}