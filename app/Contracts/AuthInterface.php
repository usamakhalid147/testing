<?php

/**
 * Interface that describe User Authentication
 *
 * @package     HyraHotel
 * @subpackage  Contracts
 * @category    Auth Interface
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Contracts;

interface AuthInterface
{
    public function createOrGetUser(Array $user_data);
    public function attemptLogin(Array $user_data,bool $remember_me);
    public function completeVerification(string $user_id,string $auth_id);
    public function diconnectVerification(string $user_id);
}