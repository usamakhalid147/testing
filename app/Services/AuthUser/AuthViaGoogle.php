<?php

/**
 * Authenticate User Via Google
 *
 * @package     HyraHotel
 * @subpackage  Services\AuthUser
 * @category    AuthViaGoogle
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services\AuthUser;

use App\Contracts\AuthInterface;
use App\Models\User;
use App\Models\UserVerification;
use Auth;

class AuthViaGoogle implements AuthInterface
{
    /**
     * Check user already exist or create new User
     *
     * @param Array $user_data
     * @return \App\Models\User $user
     */
    public function createOrGetUser(Array $user_data)
    {
    	$user = User::where('google_id',$user_data['google_id'])->orWhere('email',$user_data['email'])->first();
    	if(isset($user)) {
    		if($user->google_id == '') {
    			$user->google_id = $user_data['google_id'];
    			$user->save();
    		}

    		$this->completeVerification($user->id,$user->google_id);
    		return $user;
    	}
    	$user_data['status'] = "active";

        if($user_data['profile_picture'] != '') {
            $user_data['src'] = $user_data['profile_picture'];
            $user_data['photo_source'] = "google";
        }
        $user = User::create($user_data);

    	$this->completeVerification($user->id,$user->google_id);
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
    	return Auth::LoginUsingId($credentials['id']);
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
    	$verification = UserVerification::find($user_id);
    	if($verification->google != 1) {
    		$verification->google = 1;
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
        $verification->google = 0;
        $verification->save();
    }
}