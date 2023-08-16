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

namespace App\DataTables;

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
        ->addColumn('address_line_1', function($query) {
            return optional($query->user_information)->address_line_1 ?? '-';
        })
        ->addColumn('address_line_2', function($query) {
            return optional($query->user_information)->address_line_2 ?? '-';
        })
        ->addColumn('city', function($query) {
            return optional($query->user_information)->city ?? '-';
        })
        ->addColumn('state', function($query) {
            return optional($query->user_information)->state ?? '-';
        })
        ->addColumn('country', function($query) {
            return optional($query->user_information)->country ?? '-';
        })
        ->addColumn('postal_code', function($query) {
            return optional($query->user_information)->postal_code ?? '-';
        })
        ->addColumn('date_of_birth', function($query) {
            return optional($query->user_information)->date_of_birth ?? '-';
        })
        ->addColumn('gender', function($query) {
            return optional($query->user_information)->gender ?? '-';
        })
        ->addColumn('created_on', function($query){
            return $query->created_at->format(DATE_FORMAT.' '.TIME_FORMAT);
        })
        ->addColumn('status', function($query){
            return Lang::get('admin_messages.'.$query->status);
        })
        ->addColumn('verification_status', function ($query) {
            if ($query->verification_status == 'no') {
                return '-';
            }
            return $query->verification_status;
        })
        ->addColumn('action',function($query) {
            $user_type = $this->user_type.'s';
            $edit = getCurrentUser()->can('update-'.$user_type) ? '<a href="'.route('admin.'.$user_type.'.edit',['id' => $query->id]).'" class="h3"> <i class="fa fa-edit"></i> </a>' : '';
            $delete = getCurrentUser()->can('delete-'.$user_type) ? '<a href="" data-action="'.route('admin.'.$user_type.'.delete',['id' => $query->id]).'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" class="h3"> <i class="fa fa-trash-alt"></i> </a>' : '';
            $login_class = displayCrendentials() ? '' : 'd-none';
            $login = getCurrentUser()->can('update-'.$user_type) ? '<a href="'.route('admin.'.$user_type.'.login',['id' => $query->id]).'" target="_blank" class="h3 '.$login_class.'"> <i class="fa fas fa-sign-in-alt"></i> </a>' : '';
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
        return $model->with('user_information')->where('user_type',$this->user_type)->select();
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
        if ($this->user_type == 'user') {
            $result = [
                ['data' => 'id', 'name' => 'id', 'title' => Lang::get('messages.user_id')],
                ['data' => 'full_name', 'name' => 'full_name', 'title' => Lang::get('messages.full_name')],
                ['data' => 'email', 'name' => 'email', 'title' => Lang::get('messages.email')],
                ['data' => 'phone_number', 'name' => 'phone_number', 'title' => Lang::get('admin_messages.mobile_number')],
                ['data' => 'address_line_1', 'name' => 'address_line_1', 'title' => Lang::get('messages.home_address')],
                ['data' => 'address_line_2', 'name' => 'address_line_2', 'title' => Lang::get('messages.ward')],
                ['data' => 'state', 'name' => 'state', 'title' => Lang::get('messages.town')],
                ['data' => 'city', 'name' => 'city', 'title' => Lang::get('admin_messages.province')],
                ['data' => 'country_code', 'name' => 'country_code', 'title' => Lang::get('messages.country')],
                ['data' => 'postal_code', 'name' => 'postal_code', 'title' => Lang::get('messages.postal_code')],
                ['data' => 'date_of_birth', 'name' => 'date_of_birth', 'title' => Lang::get('messages.date_of_birth')],
                ['data' => 'gender', 'name' => 'gender', 'title' => Lang::get('messages.gender')],
                ['data' => 'created_on', 'name' => 'created_on', 'title' => Lang::get('messages.created_on')],
                ['data' => 'verification_status', 'name' => 'verification_status', 'title' => Lang::get('admin_messages.verification_status')],
                ['data' => 'status', 'name' => 'status', 'title' => Lang::get('messages.status')],
            ];
        }
        else {
            $result = [
                ['data' => 'id', 'name' => 'id', 'title' => Lang::get('messages.user_id')],
                ['data' => 'full_name', 'name' => 'full_name', 'title' => Lang::get('messages.full_name')],
                ['data' => 'title', 'name' => 'title', 'title' => Lang::get('messages.manager_titles')],
                ['data' => 'email', 'name' => 'email', 'title' => Lang::get('messages.manager_email')],
                ['data' => 'telephone_number', 'name' => 'telephone_number', 'title' => Lang::get('admin_messages.telephone_number')],
                ['data' => 'phone_number', 'name' => 'phone_number', 'title' => Lang::get('admin_messages.manager_mobile_number')],
                ['data' => 'country_code', 'name' => 'country_code', 'title' => Lang::get('messages.country_code')],
                ['data' => 'city', 'name' => 'city', 'title' => Lang::get('messages.province/city')],
                ['data' => 'date_of_birth', 'name' => 'date_of_birth', 'title' => Lang::get('messages.date_of_birth')],
                ['data' => 'gender', 'name' => 'gender', 'title' => Lang::get('messages.gender')],
                ['data' => 'created_on', 'name' => 'created_on', 'title' => Lang::get('messages.created_on')],
                ['data' => 'verification_status', 'name' => 'verification_status', 'title' => Lang::get('admin_messages.verification_status')],
                ['data' => 'status', 'name' => 'status', 'title' => Lang::get('messages.status')],
            ];
        }

        return $result;
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