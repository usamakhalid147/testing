<?php

/**
 * Permission Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    PermissionModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Shanmuga\LaravelEntrust\Models\EntrustPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends EntrustPermission
{
    use HasFactory;

    public $fillable = ['name'];

    public function getRoleTypeAttribute()
    {
        $role = explode('-',$this->name);
        $role1 = $role[1];
        if (in_array($role1,['host_hotels','host_rooms','host_coupon_codes','host_payouts','host_reservations','host_reports','host_reviews'])) {
            $role1 = str_replace('host_', '', $role1);
        } elseif ($role1  == 'admin_users' || $role1  == 'host_users') {
            $role1 = 'agents';
        } elseif ($role1  == 'roles') {
            $role1 = 'roles_privilege';
        } elseif ($role1  == 'host_roles') {
            $role1 = 'roles_privilege';
        } elseif ($role1  == 'sliders') {
            $role1 = 'home_page_sliders';
        } elseif ($role1  == 'edit_profile') {
            $role1 = 'management_profile';
        } elseif ($role1  == 'edit_company') {
            $role1 = 'company_profile';
        }

        if ($role1  == 'hotels' || $role1  == 'rooms') {
            $role1 = $role1.'_management';
        }
        return ucfirst($role[0] == 'update' ? 'Modify' : $role[0])." ".\Lang::get('admin_messages.'.$role1);
    }
}
