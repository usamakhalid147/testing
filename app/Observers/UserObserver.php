<?php

/**
 * Listen All Events on User Model
 *
 * @package     HyraHotel
 * @subpackage  Observers
 * @category    UserObserver
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Observers;

use App\Models\User;
use App\Models\ReferralUser;
use App\Models\UserVerification;
use App\Models\UserInformation;

class UserObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function created(User $user)
    {
    	$user_info = new UserInformation;
        $user_info->user_id = $user->id;
        $user_info->city = $user->city;
        $user_info->country_code = $user->country_code;
        $user_info->save();

        $verification = new UserVerification;
        $verification->user_id = $user->id;
        $verification->save();

        $user->username = strtolower(substr($user->first_name,0,10)).''.$user->id;
        $user->save();

        if(!isAdmin() && session('referral_username') != '') {
            $referred_user = User::where('username',session('referral_username'))->first();
            if($referred_user != '') {
                $user_earned_amount = ReferralUser::where('user_id',$referred_user->id)->get()->sum('max_creditable_amount');

                $referral_user = new ReferralUser;
                $referral_user->user_id = $referred_user->id;
                $referral_user->referral_user_id = $user->id;
                $referral_user->currency_code = session('currency');
                $referral_user->referral_credited_amount = referral_settings('new_referral_credit');
                $referral_user->new_referral_amount = referral_settings('new_referral_credit');
                $referral_user->user_become_guest_amount = ($user_earned_amount < referral_settings('per_user_limit')) ? referral_settings('user_become_guest_credit') : 0;
                $referral_user->save();
            }
            session()->forget('referral_user');
        }

        if ($user->user_type == 'host') {
            $config = config('entrust_seeder.role_structure');
            $userRoles = config('entrust_seeder.user_roles');
            $mapPermission = collect(config('entrust_seeder.permissions_map'));

            $modules = $config['host'];
            $key = 'host';
            // Create a new role
            $role = \App\Models\Role::create([
                'name' => $key.'_'.$user->id,
                'user_type' => $key,
                'user_id' => $user->id,
                'display_name' => ucwords(str_replace('_', ' ', $key)),
                'description' => ucwords(str_replace('_', ' ', $key))
            ]);

            $permissions = \App\Models\Permission::where('user_type',$user->user_type)->get();

            // Attach all permissions to the role
            $role->permissions()->sync($permissions);
            $user->attachRole($role);
        }
    }

    /**
     * Listen to the User updating event.
     *
     * @param  User  $user
     * @return void
     */
    public function updating(User $user)
    {
        if($user->isDirty('email')) {
            $user->load('user_verification');
            $user->user_verification->email = 0;
            $user->user_verification->save();
        }
    }
}