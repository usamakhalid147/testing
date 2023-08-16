<?php

/**
 * User Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    UserDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables\Host;

use Yajra\DataTables\Services\DataTable;
use App\Models\User;
use Lang;

class UsersDataTable extends DataTable
{
    protected $user_type;

    // set User Type
    public function setUserType($user_type)
    {
        $this->user_type = $user_type;
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
        ->addColumn('image', function ($query) {
            return '<img class="table-img my-2 rounded" src="'.$query->profile_picture_src.'" width="50" height="50">';
        })
        ->addColumn('full_name', function($query){
            return $query->full_name;
        })
        ->addColumn('telephone_number', function($query){
            return $query->telephone_number ?? '-';
        })
        ->addColumn('dob', function($query){
            return $query->user_information->dob->format(DATE_FORMAT);
        })
        ->addColumn('gender', function($query){
            return $query->user_information->gender;
        })
        ->addColumn('role', function($query) {
            return $query->role_name;
        })
        ->addColumn('status', function($query){
            return Lang::get('admin_messages.'.$query->status);
        })
        ->addColumn('action',function($query) {
            $edit = getCurrentUser()->can('update-host_users') ? '<a href="'.route('host.users.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-host_users') ? '<a href="" data-action="'.route('host.users.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            $login = getCurrentUser()->can('update-host_users') ? '<a href="'.route('host.users.login',['id' => $query->id]).'" target="_blank" class="d-none h3"> <i class="fa fas fa-sign-in-alt"></i> </a>' : '';
            return '<div class="d-flex">'.$edit.' &nbsp; '.$delete.' &nbsp; '.$login.'</div>';
        })
        ->rawColumns(['image','full_name','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        return $model->where('user_type',$this->user_type)->select();
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
            ['data' => 'full_name', 'name' => 'full_name', 'title' => Lang::get('admin_messages.full_name')],
            ['data' => 'role', 'name' => 'role', 'title' => Lang::get('admin_messages.agent_role')],
            ['data' => 'email', 'name' => 'email', 'title' => Lang::get('admin_messages.agent').' '.Lang::get('admin_messages.email')],
            ['data' => 'telephone_number', 'name' => 'telephone_number', 'title' => Lang::get('admin_messages.office_telephone_number')],
            ['data' => 'phone_number', 'name' => 'phone_number', 'title' => Lang::get('admin_messages.agent')." ".Lang::get('admin_messages.mobile_number')],
            ['data' => 'country_code', 'name' => 'country_code', 'title' => Lang::get('admin_messages.country_code')],
            ['data' => 'city', 'name' => 'city', 'title' => Lang::get('admin_messages.province')],
            ['data' => 'dob', 'name' => 'dob', 'title' => Lang::get('admin_messages.dob')],
            ['data' => 'gender', 'name' => 'gender', 'title' => Lang::get('admin_messages.gender')],
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
        return 'users_' . date('YmdHis');
    }
}