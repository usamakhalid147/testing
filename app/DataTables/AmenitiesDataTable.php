<?php

/**
 * Amenities Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    AmenitiesDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Amenity;
use Lang;

class AmenitiesDataTable extends DataTable
{
    protected $list_type;

    // set List Type
    public function setListType($list_type)
    {
        $this->list_type = $list_type;
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
        ->addColumn('status', function($query) {
            return getStatusText($query->status);
        })
        ->addColumn('image', function ($query) {
            return '<img src="'.$query->image_src.'" width="150" height="150">';
        })
        ->addColumn('action',function($query) {
            $type = $this->list_type == 'room' ? 'room_amenities' : 'hotel_amenities';
            $edit = getCurrentUser()->can('update-'.$type) ? '<a href="'.route('admin.'.$type.'.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-'.$type) ? '<a href="" data-action="'.route('admin.'.$type.'.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        })
        ->rawColumns(['image','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param Amenity $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Amenity $model)
    {
        $locale = global_settings('default_language');
        $query = $model->join('amenity_types', function($join) {
            $join->on('amenity_types.id', '=', 'amenities.amenity_type_id');
        })
        ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(amenity_types.name, \'$.'.$locale.'\')) as amenity_type, JSON_UNQUOTE(JSON_EXTRACT(amenities.name, \'$.'.$locale.'\')) as amenity_name, JSON_UNQUOTE(JSON_EXTRACT(amenities.description, \'$.'.$locale.'\')) as amenity_description,amenities.id as id,amenities.image as image,amenities.upload_driver as upload_driver,amenities.status as status')->where('list_type',$this->list_type);
        return $query;
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
            ['data' => 'amenity_name', 'name' => 'amenities.name', 'title' => Lang::get('admin_messages.description')],
            ['data' => 'amenity_type', 'name' => 'amenity_types.name', 'title' => Lang::get('admin_messages.amenity_type')],
            ['data' => 'image', 'name' => 'image', 'title' => Lang::get('admin_messages.image'),'searchable' => false,'orderable' => false],
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
        return 'amenities_' . date('YmdHis');
    }
}