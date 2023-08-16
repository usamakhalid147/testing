<?php

/**
 * Calculate Payout & Refund for Flexible Cancellation
 *
 * @package     HyraHotel
 * @subpackage  Services\CancellationPolicies
 * @category    FlexibleCancellation
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services\CancellationPolicies;

use Illuminate\Http\Request;
use App\Contracts\CancellationPolicyContract;
use App\Models\Reservation;
use App\Models\RoomReservation;
use App\Traits\ReservationCancellation;

class FlexibleCancellation implements CancellationPolicyContract
{
	use ReservationCancellation;

	/**
     * Constructor
     *
     */
	function __construct(string $user_type, $reservation,$room_reservations)
	{
		$this->user_type = $user_type;
		$this->reservation = $reservation;
		$this->room_reservations = $room_reservations;
		$this->calc_result = array(
			'guest_refund_amount' => "0.00",
			'host_payout_amount' => "0.00",
			'currency_code' => $reservation->currency_code,
		);
	}

	/**
     * Host Cancel Reservation calculation
     *
     */
	protected function HostCancelCalculation()
	{
		$host_payout_amount = 0;
		$guest_refund_amount = 0;

		$date_diff = now()->diffInDays(getDateObject($this->reservation['checkin']))+1;
		$room_reservations = $this->room_reservations;
		
		foreach($room_reservations as $room_reservation) {
			$cancellation_policies = collect(json_decode($room_reservation->cancellation_policy,true));
			$cancellation_policy = $cancellation_policies->where('days', '>=', $date_diff)->sortBy('days')->first();
			
			if(!is_array($cancellation_policy)) {
				$cancellation_policy = [
				    "days" => $date_diff,
				    "percentage" => 100,
				];
			}
			
            $refund_amount = calculatePercentageAmount($cancellation_policy['percentage'],$room_reservation->total_price);
          	$payout_amount = $room_reservation->total_price - $refund_amount;
          	
          	if($payout_amount > 0) {
          		$host_fee = $this->calcHostFee($payout_amount);
				$room_reservation->host_fee = currencyConvert($host_fee,$room_reservation->currency_code,$room_reservation->getRawOriginal('currency_code'));
				$room_reservation->host_payout_status = 'future';
				$room_reservation->host_payout_amount = $payout_amount - $host_fee;
				$room_reservation->save();
          	}

          	if($refund_amount > 0) {
				$room_reservation->guest_refund_status = 'future';
				$room_reservation->guest_refund_amount = $refund_amount;
				$room_reservation->save();
          	}
		}

		$room_reservations = RoomReservation::where('reservation_id',$this->reservation->id)->get();

		$guest_refund_amount = $room_reservations->where('guest_refund_status','future')->values()->sum('guest_refund_amount');
		$host_payout_amount = $room_reservations->where('host_payout_status','future')->values()->sum('host_payout_amount');

		if($host_payout_amount > 0) {
			$host_fee = $this->calcHostFee($host_payout_amount);
			$this->reservation->host_fee = currencyConvert($host_fee,$this->reservation->currency_code,$this->reservation->getRawOriginal('currency_code'));
		}

		$this->calc_result['guest_refund_amount'] = $this->formatAmount($guest_refund_amount);
		$this->calc_result['host_payout_amount'] = $this->formatAmount($host_payout_amount);

		return true;
	}

	/**
     * Guest Cancel Reservation calculation
     *
     */
	protected function guestCancelCalculation()
	{
		$host_payout_amount = 0;
		$guest_refund_amount = 0;

		$date_diff = now()->diffInDays(getDateObject($this->reservation['checkin']))+1;
		$room_reservations = $this->room_reservations;
		
		foreach($room_reservations as $room_reservation) {
			$cancellation_policies = collect(json_decode($room_reservation->cancellation_policy,true));
			$cancellation_policy = $cancellation_policies->where('days', '>=', $date_diff)->sortBy('days')->first();
			
			if(!is_array($cancellation_policy)) {
				$cancellation_policy = [
				    "days" => $date_diff,
				    "percentage" => 100,
				];
			}
			
            $refund_amount = calculatePercentageAmount($cancellation_policy['percentage'],$room_reservation->total_price);
          	$payout_amount = $room_reservation->total_price - $refund_amount;
          	
          	if($payout_amount > 0) {
          		$host_fee = $this->calcHostFee($payout_amount);
				$room_reservation->host_fee = currencyConvert($host_fee,$room_reservation->currency_code,$room_reservation->getRawOriginal('currency_code'));
				$room_reservation->host_payout_status = 'future';
				$room_reservation->host_payout_amount = $payout_amount - $host_fee;
				$room_reservation->save();
          	}

          	if($refund_amount > 0) {
				$room_reservation->guest_refund_status = 'future';
				$room_reservation->guest_refund_amount = $refund_amount;
				$room_reservation->save();
          	}
		}

		$room_reservations = RoomReservation::where('reservation_id',$this->reservation->id)->get();

		$guest_refund_amount = $room_reservations->where('guest_refund_status','future')->values()->sum('guest_refund_amount');
		$host_payout_amount = $room_reservations->where('host_payout_status','future')->values()->sum('host_payout_amount');
		$host_fee = $room_reservations->where('host_payout_status','future')->values()->sum('host_fee');
		if($host_fee > 0) {
			$this->reservation->host_fee = currencyConvert($host_fee,$this->reservation->currency_code,$this->reservation->getRawOriginal('currency_code'));
			$this->reservation->save();
		}
		$this->calc_result['guest_refund_amount'] = $this->formatAmount($guest_refund_amount);
		$this->calc_result['host_payout_amount'] = $this->formatAmount($host_payout_amount);

		return true;
	}

	/**
     * Calculate payout and refund amount
     *
     */
	public function calcPayoutRefundAmount()
	{
		if($this->user_type == 'Host') {
			$this->updateHostPenalty();
		}
		
		if($this->isPaymentNotCompleted()) {
			return $this->getReturnData();
		}

		$cancel_function = $this->user_type."CancelCalculation";
		$this->$cancel_function();

		return $this->getReturnData();
	}
}