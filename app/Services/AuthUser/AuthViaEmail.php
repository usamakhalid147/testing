<?php

/**
 * Authenticate User Via Email
 *
 * @package     HyraHotel
 * @subpackage  Services\AuthUser
 * @category    AuthViaEmail
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services\AuthUser;

use App\Contracts\AuthInterface;
use App\Models\User;
use Auth;

class AuthViaEmail implements AuthInterface
{
    /**
     * Check user already exist or create new User
     *
     * @param Array $user_data
     * @return \App\Models\User $user
     */
    public function createOrGetUser(Array $user_data)
    {
        $user = User::create($user_data);
        $user_info = $user->user_information;
        $user_info->gender = $user_data['gender'] ?? NULL;
        $user_info->dob = $user_data['dob'] ?? NULL;
        $user_info->address_line_1 = $user_data['address_line_1'] ?? NULL;
        $user_info->address_line_2 = $user_data['address_line_2'] ?? NULL;
        $user_info->state = $user_data['state'] ?? NULL;
        $user_info->city = $user_data['city'];
        $user_info->country_code = $user_data['country_code'];
        $user_info->postal_code = $user_data['postal_code'] ?? NULL;
        $user_info->save();
        
        resolveAndSendNotification("confirmUserEmail",$user->id);
        return $user;
    }

    /**
     * Authenticate
     *
     * @param Array $credentials
     * @param Boolean $remember_me
     * @return \Auth
     */
    public function attemptLogin(Array $credentials,bool $remember_me = false)
    {
    	return Auth::attempt($credentials,$remember_me);
    }

    /**
     * complete Verification
     *
     * @param String $user_id
     * @param String $auth_id
     * @return Void
     */
    public function completeVerification(string $user_id, string $auth_id)
    {
    	$user = User::find($user_id);
        $verification = $user->user_verification;
    	if($verification->email != 1) {
    		$verification->email = 1;
    		$verification->save();
    	}
    }

    /**
     * diconnect Verification
     *
     * @param String $user_id
     * @return Void
     */
    public function diconnectVerification(string $user_id)
    {
        $verification = UserVerification::find($user_id);
        $verification->email = 0;
        $verification->save();
    }
}