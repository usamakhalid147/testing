<?php

/**
 * Amenity Types Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    AmenityTypesDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\AmenityType;
use Lang;

class AmenityTypesDataTable extends DataTable
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
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-amenity_types') ? '<a href="'.route('admin.amenity_types.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-amenity_types') ? '<a href="" data-action="'.route('admin.amenity_types.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param AmenityType $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(AmenityType $model)
    {
        $locale = global_settings('default_language');
        $query = $model->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(amenity_types.name, \'$.'.$locale.'\')) as amenity_type_name, JSON_UNQUOTE(JSON_EXTRACT(amenity_types.description, \'$.'.$locale.'\')) as amenity_description,amenity_types.id as id,amenity_types.status as status');
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
            ['data' => 'amenity_type_name', 'name' => 'amenity_types.name', 'title' => Lang::get('admin_messages.amenity_types')],
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
        return 'amenity_types_' . date('YmdHis');
    }
}