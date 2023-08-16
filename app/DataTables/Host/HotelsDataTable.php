<?php

/**
 * Hotels Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables\Host
 * @category    HotelsDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables\Host;

use Yajra\DataTables\Services\DataTable;
use App\Models\Hotel;
use Lang;
use Auth;

class HotelsDataTable extends DataTable
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
        ->addColumn('created_at', function ($query) {
            return $query->created_at->format(DATE_FORMAT);
        })
        ->addColumn('hotel_name', function($query) {
            return '<a href="'.resolveRoute('hotel_details',['id' => $query->id]).'" target="_blank"> '.$query->name.' </a>';
        })
        ->addColumn('property_type', function($query) {
            return $query->property_type_name;
        })
        ->addColumn('property_address', function($query) {
            return $query->hotel_address->address_line_1;
        })
        ->addColumn('ward', function($query) {
            return $query->hotel_address->address_line_2;
        })
        ->addColumn('city', function($query) {
            return $query->hotel_address->city;
        })
        ->addColumn('state', function($query) {
            return $query->hotel_address->state;
        })
        ->addColumn('country_code', function($query) {
            return $query->hotel_address->country_code;
        })
        ->addColumn('postal_code', function($query) {
            return $query->hotel_address->postal_code;
        })
        ->addColumn('hotel_status', function ($query) {
            if($query->completed_percent != 100 || $query->subroom_count == 0) {
                return Lang::get('admin_messages.not_completed');
            }

            if(!in_array($query->status,['Listed','Unlisted'])) {
                return $query->status;
            }

            return $query->status;
        })
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-host_hotels') ? '<a class="info me-2 h3" href="'.route('host.hotels.edit',['id' => $query->id]).'" class=""> <i class="fa fa-edit"></i> </a>' : '';
            $manage_rooms = getCurrentUser()->can('view-host_rooms') ? '<a class="me-2 h3" target="_blank" href="'.route('host.rooms',['id' => $query->id]).'"> <i class="fa fa-th-list"></i> </a>': '';
            $delete = getCurrentUser()->can('delete-host_hotels') ? '<a class="info me-2 h3" href="" data-action="'.route('host.hotels.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return "<div class='d-flex'>".$edit." &nbsp; ".$manage_rooms." &nbsp; ".$delete."</div>";
        })
        ->rawColumns(['hotel_name','host_name','total_rooms','verified','hotel_status','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param Hotel $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Hotel $model)
    {
        return $model->with('hotel_address')->authUser()->get();
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

            ['data' => 'id', 'name' => 'id', 'title' => Lang::get('messages.property_id')],
            ['data' => 'hotel_name', 'name' => 'hotel_name', 'title' => Lang::get('admin_messages.property').' '.Lang::get('admin_messages.name')],
            ['data' => 'star_rating', 'name' => 'star_rating', 'title' => Lang::get('admin_messages.property_star_rating')],
            ['data' => 'property_type', 'name' => 'property_type', 'title' => Lang::get('admin_messages.property_type')],
            ['data' => 'tele_phone_number', 'name' => 'tele_phone_number', 'title' => Lang::get('admin_messages.property_telephone_number')],
            ['data' => 'extension_number', 'name' => 'extension_number', 'title' => Lang::get('admin_messages.extension_number')],
            ['data' => 'fax_number', 'name' => 'fax_number', 'title' => Lang::get('admin_messages.fax_number')],
            ['data' => 'property_address', 'name' => 'property_address', 'title' => Lang::get('admin_messages.property_address')],
            ['data' => 'ward', 'name' => 'ward', 'title' => Lang::get('admin_messages.ward')],
            ['data' => 'state', 'name' => 'state', 'title' => Lang::get('messages.town')],
            ['data' => 'city', 'name' => 'city', 'title' => Lang::get('admin_messages.province')],
            ['data' => 'country_code', 'name' => 'country_code', 'title' => Lang::get('messages.country')],
            ['data' => 'postal_code', 'name' => 'postal_code', 'title' => Lang::get('messages.postal_code')],
            ['data' => 'website', 'name' => 'website', 'title' => Lang::get('admin_messages.property_website')],
            ['data' => 'email', 'name' => 'email', 'title' => Lang::get('admin_messages.property_email')],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => Lang::get('admin_messages.created_on')],
            ['data' => 'hotel_status', 'name' => 'hotel_status', 'title' => Lang::get('admin_messages.status')],
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
        return 'hotels_' . date('YmdHis');
    }
}