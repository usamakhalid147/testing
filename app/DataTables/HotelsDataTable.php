<?php

/**
 * Hotels Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    HotelsDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Hotel;
use Lang;

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
        ->addColumn('company_id', function ($query) {
            return getCurrentUser()->can('update-hosts') ? '<a class="common-link" href="'.route('admin.hosts.edit',['id' => $query->user_id]).'" target="_blank"> '.$query->user_id.'</i> </a>' : $query->user_id;
        })
        ->addColumn('company_name', function ($query) {
            return optional($query->company)->company_name ?? '';
        })
        ->addColumn('company_tax_number', function ($query) {
            return optional($query->company)->company_tax_number ?? '';
        })
        ->addColumn('company_tele_phone_number', function ($query) {
            return optional($query->company)->company_tele_phone_number ?? '';
        })
        ->addColumn('company_fax_number', function ($query) {
            return optional($query->company)->company_fax_number ?? '';
        })
        ->addColumn('company_address', function ($query) {
            return optional($query->company)->address_line_1 ?? '';
        })
        ->addColumn('company_ward', function ($query) {
            return optional($query->company)->address_line_2 ?? '';
        })
        ->addColumn('company_website', function ($query) {
            return optional($query->company)->company_website ?? '';
        })
        ->addColumn('company_email', function ($query) {
            return optional($query->company)->company_email ?? '';
        })
        ->addColumn('hotel_name', function ($query) {
            return '<a href="'.resolveRoute('hotel_details',['id' => $query->id]).'" target="_blank"> '.$query->name.' </a>';
        })
        ->addColumn('created_at', function ($query) {
            return $query->created_at->format(DATE_FORMAT.' '.TIME_FORMAT);
        })
        ->addColumn('property_type', function ($query) {
            return $query->property_type_name;
        })
        ->addColumn('address_line_1', function ($query) {
            return optional($query->hotel_address)->address_line_1 ?? '';
        })
        ->addColumn('address_line_2', function ($query) {
            return optional($query->hotel_address)->address_line_2 ?? '';
        })
        ->addColumn('city', function ($query) {
            return optional($query->hotel_address)->city ?? '';
        })
        ->addColumn('state', function ($query) {
            return optional($query->hotel_address)->state ?? '';
        })
        ->addColumn('country_code', function ($query) {
            return optional($query->hotel_address)->country_name ?? '';
        })
        ->addColumn('postal_code', function ($query) {
            return optional($query->hotel_address)->postal_code ?? '';
        })
        ->addColumn('hotel_status', function ($query) {
            $status = $query->status;
            if ($status == null) {
               $status = 'Pending';
            }
            return $status;
        })
        ->addColumn('recommended', function ($query) {
            $class = ($query->is_recommended) ? 'success' : 'danger';
            $is_recommended = ($query->is_recommended) ? Lang::get('admin_messages.yes'):Lang::get('admin_messages.no');
            $recommended = '<button class="hotel-recommended btn btn-xs btn-'.$class.'"  data-id="'.$query->id.'">'.$is_recommended.'</button>';

            return $recommended;
        })
        ->addColumn('top_picks', function ($query) {
            $class = ($query->is_top_picks) ? 'success' : 'danger';
            $is_top_picks = ($query->is_top_picks) ? Lang::get('admin_messages.yes'):Lang::get('admin_messages.no');
            $top_picks = '<button class="hotel-top_picks btn btn-xs btn-'.$class.'"  data-id="'.$query->id.'">'.$is_top_picks.'</button>';

            return $top_picks;
        })
        ->addColumn('verified', function ($query) {
            if($query->completed_percent != 100) {
                return Lang::get('admin_messages.not_completed');
            }
            $verification_options = array(
                'Pending'   => Lang::get('admin_messages.pending'),
                'Approved'  => Lang::get('admin_messages.approved'),
                'Resubmit'  => Lang::get('admin_messages.resubmit'),
            );
            $verified =  '<select class="form-select datatable-select hotel-admin_status" data-id="'.$query->id.'">';
            foreach ($verification_options as $key => $value) {
                $selected = ($query->admin_status == $key) ? 'selected' : '';
                $verified .= '<option value="'.$key.'" '.$selected.'>'. $value .'</option>';
            }
            $verified .= '</select>';

            return $verified;
        })
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-hotels') ? '<a class="me-2 h3" href="'.route('admin.hotels.edit',['id' => $query->id]).'"> <i class="fa fa-edit"></i> </a>' : '';
            $manage_rooms = getCurrentUser()->can('view-rooms') ? '<a class="me-2 h3" target="_blank" href="'.route('admin.rooms',['id' => $query->id]).'"> <i class="fa fa-th-list"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-hotels') ? '<a class="me-2 h3" href="" data-action="'.route('admin.hotels.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return "<div class='d-flex'> &nbsp; ".$edit." &nbsp; ".$manage_rooms." &nbsp; ".$delete."</div>";
        })
        ->rawColumns(['company_id','hotel_name','image','recommended','verified','action','top_picks']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param Hotel $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Hotel $model)
    {
        return $model->whereNull('deleted_at')->get();
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
            ['data' => 'tele_phone_number', 'name' => 'tele_phone_number', 'title' => Lang::get('messages.tele_phone_number')],
            ['data' => 'extension_number', 'name' => 'extension_number', 'title' => Lang::get('messages.extension_number')],
            ['data' => 'fax_number', 'name' => 'fax_number', 'title' => Lang::get('messages.fax_number')],
            ['data' => 'address_line_1', 'name' => 'address_line_1', 'title' => Lang::get('admin_messages.property_address')],
            ['data' => 'address_line_2', 'name' => 'address_line_2', 'title' => Lang::get('admin_messages.ward')],
            ['data' => 'state', 'name' => 'state', 'title' => Lang::get('messages.town')],
            ['data' => 'city', 'name' => 'city', 'title' => Lang::get('messages.city')],
            ['data' => 'country_code', 'name' => 'country_code', 'title' => Lang::get('messages.country')],
            ['data' => 'postal_code', 'name' => 'postal_code', 'title' => Lang::get('messages.postal_code')],
            ['data' => 'website', 'name' => 'website', 'title' => Lang::get('messages.website')],
            ['data' => 'email', 'name' => 'email', 'title' => Lang::get('messages.email')],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => Lang::get('admin_messages.created_on')],
            ['data' => 'hotel_status', 'name' => 'hotel_status', 'title' => Lang::get('admin_messages.status')],
            ['data' => 'verified', 'name' => 'verified', 'title' => Lang::get('admin_messages.is_verified')],
            ['data' => 'recommended', 'name' => 'recommended', 'title' => Lang::get('admin_messages.is_recommended')],
            ['data' => 'top_picks', 'name' => 'top_picks', 'title' => Lang::get('messages.top_picks')],
            ['data' => 'company_id', 'name' => 'company_id', 'title' => Lang::get('admin_messages.company_id')],
            ['data' => 'company_name', 'name' => 'company_name', 'title' => Lang::get('admin_messages.company_name')],
            ['data' => 'company_tax_number', 'name' => 'company_tax_number', 'title' => Lang::get('messages.company_tax_number')],
            ['data' => 'company_tele_phone_number', 'name' => 'company_tele_phone_number', 'title' => Lang::get('messages.company_tele_phone_number')],
            ['data' => 'company_fax_number', 'name' => 'company_fax_number', 'title' => Lang::get('messages.company_fax_number')],
            ['data' => 'company_address', 'name' => 'company_address', 'title' => Lang::get('messages.company_address')],
            ['data' => 'company_ward', 'name' => 'company_ward', 'title' => Lang::get('messages.company_ward')],
            ['data' => 'company_website', 'name' => 'company_website', 'title' => Lang::get('messages.company_website')],
            ['data' => 'company_email', 'name' => 'company_email', 'title' => Lang::get('messages.company_email')],
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