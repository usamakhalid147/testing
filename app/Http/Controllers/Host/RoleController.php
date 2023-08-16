<?php

/**
 * Role Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Host
 * @category    RoleController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Host;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\Host\RolesDataTable;
use App\Models\Role;
use App\Models\Permission;
use Lang;

class RoleController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_roles_privilege');
        $this->view_data['active_menu'] = 'roles_privilege';
        $this->view_data['user_type'] = $this->user_type = 'host';
        $this->view_data['sub_title'] = Lang::get('admin_messages.host_roles');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RolesDataTable $dataTable)
    {
        return $dataTable->render('host.roles.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_role');
        $this->view_data['result'] = new Role;
        $config = config('entrust_seeder.role_structure');
        $this->view_data['all_permissions'] = Permission::where('user_type',$this->user_type)->get();
        $this->view_data['permissions'] = $config[$this->user_type];
        $this->view_data['old_permissions'] = array();
        return view('host.roles.add', $this->view_data);
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
        
        $role = Role::create([
            'name' => $request->name,
            'user_type' => $this->user_type,
            'user_id' => getCurrentUserId(),
            'display_name' => $request->name,
            'description' => $request->description
        ]);

        $permission = $request->permission;
        $permissions = Permission::where('user_type',$this->user_type)->whereIn('id',$permission)->get();

        $role->permissions()->sync($permissions);
        
        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
        return redirect()->route('host.roles');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_role');
        $this->view_data['result'] = Role::where('user_type',$this->user_type)->findOrFail($id);
        $config = config('entrust_seeder.role_structure');
        $this->view_data['all_permissions'] = Permission::where('user_type',$this->user_type)->get();
        $this->view_data['permissions'] = $config[$this->user_type];
        $this->view_data['old_permissions'] = \DB::table('permission_role')->where('role_id',$id)->pluck('permission_id')->toArray();
        return view('host.roles.edit', $this->view_data);
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
        $can_destroy = $this->canDestroy($id);
        
        if(!$can_destroy['status']) {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy['status_message']);
            return redirect()->route('host.roles');
        }
        
        $this->validateRequest($request,$id);
        
        $role = Role::where('user_type',$this->user_type)->where('user_id',getCurrentUserId())->find($id);
        $role->name = $request->name;
        $role->display_name = $request->name;
        $role->description = $request->description;
        $role->save();

        $permission = $request->permission;
        $permissions = Permission::where('user_type',$this->user_type)->whereIn('id',$permission)->get();

        $role->permissions()->sync($permissions);

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));

        return redirect()->route('host.roles');
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
            return redirect()->route('host.roles');
        }
        
        try {
            $role = Role::where('user_type',$this->user_type)->where('user_id',getCurrentUserId())->find($id);
            $role->users()->sync([]);
            $role->permissions()->sync([]);
            $role->forceDelete();
            
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        catch (\Exception $e) {
            flashMessage('danger',Lang::get('admin_messages.failed'),$e->getMessage());
        }

        return redirect()->route('host.roles');
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
            'name' => 'required|unique:roles,name,'.$id,
            'description' => 'required',
            'permission' => 'required'
        );
        $attributes = array(
            'name' => Lang::get('admin_messages.name'),
            'description' => Lang::get('admin_messages.description'),
            'permission' => Lang::get('admin_messages.permission'),
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
        $admin_count = \DB::table('role_user')->where('user_id',getCurrentUserId())->where('role_id',$id)->count();
        if($admin_count == 0) {
            return ['status' => true,'status_message' => ''];
        }
        return ['status' => false, 'status_message' => Lang::get('admin_messages.some_user_used_role')];

    }
}
