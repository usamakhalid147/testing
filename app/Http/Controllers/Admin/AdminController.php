<?php

/**
 * Admin User Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    AdminUserController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\AdminUsersDataTable;
use App\Models\Admin;
use App\Models\Role;
use Lang;

class AdminController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title']  = Lang::get('admin_messages.manage_admin_users');
        $this->view_data['active_menu'] = 'admin_users';
        $this->view_data['sub_title'] = Lang::get('admin_messages.admin_users');;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AdminUsersDataTable $dataTable)
    {
        return $dataTable->render('admin.admin_users.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_admin_user');
        $this->view_data['result'] = new Admin;
        $this->view_data['roles'] = Role::where('user_type','admin')->get()->pluck('name','id');
        return view('admin.admin_users.add', $this->view_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        $admin = new Admin;
        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->password = $request->password;
        $admin->primary = $request->primary;
        $admin->user_currency = $request->user_currency;
        $admin->user_language = $request->user_language;
        $admin->status = $request->status;
        $admin->save();

        $admin->attachRole($request->role);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));

        return redirect()->route('admin.admin_users');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title']   = $this->sub_title = 'Edit Agent';
        $this->view_data['result'] = Admin::findOrFail($id);
        $this->view_data['result']->load('roles');
        $this->view_data['role_id'] = $this->view_data['result']->roles->first()->id;
        $this->view_data['roles'] = Role::where('user_type','admin')->get()->pluck('name','id');
        return view('admin.admin_users.edit', $this->view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateRequest($request, $id);

        $admin = Admin::Find($id);
        $admin->username = $request->username;
        $admin->email = $request->email;
        if($request->filled('password')) {
            $admin->password = $request->password;
        }
        $admin->primary = $request->primary;
        $admin->user_currency = $request->user_currency;
        $admin->user_language = $request->user_language;
        $admin->status = $request->status;
        $admin->save();

        $admin->detachRoles();
        $admin->attachRole($request->role);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));

        return redirect()->route('admin.admin_users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $can_destroy = $this->canDestroy($id);
        
        if(!$can_destroy['status']) {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy['status_message']);
            return redirect()->route('admin.admin_users');
        }
        
        try {
            Admin::where('id',$id)->delete();
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        catch (\Exception $e) {
            flashMessage('danger',Lang::get('admin_messages.failed'),$e->getMessage());
        }

        return redirect()->route('admin.admin_users');
    }

    /**
     * Check the specified resource Can be deleted or not.
     *
     * @param  Illuminate\Http\Request $request_data
     * @param  Int $id
     * @return Array
     */
    protected function validateRequest($request_data, $id = '')
    {
        $rules = array(
            'username' => 'required|max:20|unique:admins,username,'.$id,
            'password' => 'required|min:8',
            'email' => 'required|email|unique:admins,email,'.$id,
            'role' => 'required|integer',
            'primary' => 'required',
            'user_currency' => 'required',
            'user_language' => 'required',
            'status' => 'required',
        );
        if($id != '') {
            $rules['password'] = 'nullable|min:8';
        }

        $attributes = array(
            'username' => Lang::get('admin_messages.username'),
            'password' => Lang::get('admin_messages.password'),
            'email' => Lang::get('admin_messages.email'),
            'role' => Lang::get('admin_messages.role'),
            'primary' => Lang::get('admin_messages.primary'),
            'status' => Lang::get('admin_messages.status'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }

    /**
     * Check the specified resource Can be deleted or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy($id)
    {
        $admin_count = Admin::activeOnly()->where('id','!=',$id)->count();

        if($admin_count == 0) {
            return ['status' => false, 'status_message' => Lang::get('admin_messages.only_one_admin')];
        }

        return ['status' => true,'status_message' => ''];
    }
}
