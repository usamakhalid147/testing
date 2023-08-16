<?php

/**
 * Featured Cities Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    FeaturedCitiesDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\FeaturedCity;
use Lang;

class FeaturedCitiesDataTable extends DataTable
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
            $edit = getCurrentUser()->can('update-featured_cities') ? '<a href="'.route('admin.featured_cities.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-featured_cities') ? '<a href="javascript:;" data-action="'.route('admin.featured_cities.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        })
        ->rawColumns(['image','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param FeaturedCity $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(FeaturedCity $model)
    {
        return $model->select();
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
            ['data' => 'city_name', 'name' => 'city_name', 'title' => Lang::get('admin_messages.city_name')],
            ['data' => 'display_name', 'name' => 'display_name', 'title' => Lang::get('admin_messages.display_name')],
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
        return 'featured_cities_' . date('YmdHis');
    }
}