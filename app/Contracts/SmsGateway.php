<?php

/**
 * Interface that describe send SMS Functionality
 *
 * @package     HyraHotel
 * @subpackage  Contracts
 * @category    SMS Gateway
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Contracts;

interface SmsGateway
{
    public function send($number,Array $data);
}