<?php

/**
 * Property Types Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    PropertyTypesDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\PropertyType;
use Lang;

class PropertyTypesDataTable extends DataTable
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
            $edit = getCurrentUser()->can('update-property_types') ? '<a href="'.route('admin.property_types.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-property_types') ? '<a href="" data-action="'.route('admin.property_types.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        })
        ->rawColumns(['image','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param PropertyType $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PropertyType $model)
    {
        $locale = global_settings('default_language');
        $query = $model->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(property_types.name, \'$.'.$locale.'\')) as property_type_name, property_types.id as id,property_types.image as image,property_types.upload_driver as upload_driver,property_types.status as status');
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
            ['data' => 'property_type_name', 'name' => 'property_types.name', 'title' => Lang::get('admin_messages.property_types').' '.Lang::get('admin_messages.title')],
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
        return 'property_types_' . date('YmdHis');
    }
}