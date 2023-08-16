<?php

/**
 * Sliders Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    SlidersDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Slider;
use Lang;

class SlidersDataTable extends DataTable
{
    protected $type;

    /**
     * Set the value for Type
     *
     */
    public function setType($type)
    {
        $this->type = $type;
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
            return '<img class="dt-thumb-image" src="'.$query->image_src.'">';
        })
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-sliders') ? '<a href="'.route('admin.sliders.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-sliders') ? '<a href="" data-action="'.route('admin.sliders.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            return $edit." &nbsp; ".$delete;
        })
        ->rawColumns(['image','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param Slider $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Slider $model)
    {
        $locale = global_settings('default_language');
        $query = $model->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(sliders.title, \'$.'.$locale.'\')) as slider_title, sliders.id as id,sliders.image as image,sliders.upload_driver as upload_driver, sliders.order_id as order_id, sliders.status as status');
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
            ['data' => 'order_id', 'name' => 'order_id', 'title' => Lang::get('admin_messages.order_id')],
            ['data' => 'slider_title', 'name' => 'sliders.title', 'title' => Lang::get('admin_messages.title')],
            ['data' => 'image', 'name' => 'image', 'title' => Lang::get('admin_messages.image'),'searchable' => false],
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
        return 'sliders_' . date('YmdHis');
    }
}