<?php

/**
 * Room Types Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    RoomTypesDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\RoomType;
use Lang;

class RoomTypesDataTable extends DataTable
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
            return '<img src="'.$query->image_src.'" width="150" height="150">';
        })
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-room_types') ? '<a href="'.route('admin.room_types.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-room_types') ? '<a href="" data-action="'.route('admin.room_types.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        })
        ->rawColumns(['image','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param RoomType $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RoomType $model)
    {
        $locale = global_settings('default_language');
        $query = $model->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(room_types.name, \'$.'.$locale.'\')) as room_type_name, room_types.id as id,room_types.image as image,room_types.upload_driver as upload_driver,room_types.status as status');
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
            ['data' => 'id', 'name' => 'id', 'title' => Lang::get('admin_messages.id')],
            ['data' => 'room_type_name', 'name' => 'room_types.name', 'title' => Lang::get('messages.room').' '.Lang::get('admin_messages.category').' '.Lang::get('admin_messages.title')],
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
        return 'room_types_' . date('YmdHis');
    }
}