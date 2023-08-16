<?php

/**
 * Interface that describe Cancellation Policy
 *
 * @package     HyraHotel
 * @subpackage  Contracts
 * @category    Cancellation Policy
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Contracts;

use Illuminate\Http\Request;

interface CancellationPolicyContract
{
	function __construct(string $user_type,$reservation,$room_reservations);
	function setCancelReason(string $reason);
	function calcPayoutRefundAmount();
	function getReturnData();
}