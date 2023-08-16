<?php

/**
 * Trait for Both Guest and Host Cancellation
 *
 * @package     HyraHotel
 * @subpackage  Traits
 * @category    ReservationCancellation
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Traits;

use App\Models\Reservation;
use App\Models\HotelCalendar;

trait ReservationCancellation
{
    protected $currentDateTime;

    /**
     * Get Current Carbon Object
     *
     */
    protected function getCurrentDateTime()
    {
        if(!isset($this->currentDateTime)) {
            $this->currentDateTime = now();
        }
        return $this->currentDateTime;
    }

    /**
     * Check Weather Payment is Complete Or Not
     *
     */
    protected function isPaymentNotCompleted()
    {
        return ($this->reservation->status != 'Accepted');
    }

    /**
     * Get Reservation Checkin with time
     *
     */
    protected function getReservationCheckin()
    {
        $checkin_at = '';
        if($this->reservation->checkin_at != 'flexible') {
            $checkin_at = $this->reservation->checkin_at;
        }
        return getDateObject($this->reservation->getRawOriginal('checkin').' '.$checkin_at,'Y-m-d H:i:s');
    }

    /**
     * Check Current Date Time is crossed Check In Date Time
     *
     */
    protected function isAfterCheckin()
    {
        $checkin = $this->getReservationCheckin();
        $currentDateTime = $this->getCurrentDateTime();
        return ($currentDateTime >= $checkin);
    }

    /**
     * Check Reserve date is Before given days
     * 
     * @param Integer $numDays Number of days
     *
     */
    protected function isBeforeDays($numDays)
    {
        $checkin = $this->getReservationCheckin();
        $before_checkin = $checkin->subDays($numDays);
        $currentDateTime = $this->getCurrentDateTime();

        return ($currentDateTime >= $before_checkin);
    }

    /**
     * Mark Reservation date available after cancel
     *
     * @param App\Models\Reservation $reservation
     * 
     * @return void
     */
    public function updateCalendarDates()
    {
        $booking_dates = getDays($this->reservation->getRawOriginal('checkin'), $this->reservation->getRawOriginal('checkout'));
        // Remove Last Day Because Night Booking
        array_pop($booking_dates);
        
        foreach($booking_dates as $date) {
            foreach($this->reservation->room_reservations as $room_reservation) {
                $hotel_calendar = \App\Models\HotelRoomCalendar::where('reserve_date',$date)->where('hotel_id',$this->reservation->hotel_id)->where('user_id',$this->reservation->host_id)->where('room_id',$room_reservation->room_id)->first();
                $hotel_calendar->save();

            }
            
        }
    }

    /**
     * Update Reservation status As Cancelled
     *
     */
    protected function updateCancelledStatus()
    {
        $this->room_reservations->each(function($room_reservation) {
            $room_reservation->status = 'Cancelled';
            $room_reservation->cancelled_by = $this->user_type;
            $room_reservation->cancelled_at = now();
            $room_reservation->save();
        });

        $accepted_count = $this->reservation->room_reservations->where('status','Accepted')->count();
        if($accepted_count == 0) {
            $this->reservation->status = "Cancelled";
            $this->reservation->cancelled_by = $this->user_type;
            $this->reservation->cancelled_at = now();
            $this->reservation->save();
        }
    }

    /**
     * Set Cancel Reason for Reservation
     *
     */
    public function setCancelReason(string $reason)
    {
        $this->reservation->cancel_reason = $reason;
    }

    /**
     * Update Host Penalty Amount
     *
     */
    public function updateHostPenalty()
    {
        if(!$this->reservation->penalty_enabled) {
            return $this->reservation;
        }

        $host_cancel_limit = fees('host_cancel_limit');
        $host_cancel_count = Reservation::cancelCount($this->reservation->host_id)->count();

        if($host_cancel_count >= $host_cancel_limit) {
            $penalty = $this->isBeforeDays(fees('penalty_days')) ? "cancel_before_days" : "cancel_after_days";
            $this->reservation->host_penalty = currencyConvert(fees($penalty),global_settings('default_currency'),$this->reservation->getRawOriginal("currency_code"));
            updateUserPenalty($this->reservation->host_id,$this->reservation->currency_code,$this->reservation->host_penalty);
        }
        return $this->reservation;
    }

    /**
     * Update Cancelled Status And Return Calculated Data
     *
     */
    public function getReturnData()
    {
        $this->updateCancelledStatus();
        $this->updateCalendarDates();
        return $this->calc_result;
    }

    /**
     * Calculate Host Fee for given total
     *
     * @param Float $price
     * @return Float $host_fee
     */
    public function calcHostFee($price)
    {
        return calculatePercentageAmount(fees("host_fee"),$price);
    }

    /**
     * Check And Return Amount, If number is Negative then return 0
     *
     */
    public function formatAmount($amount)
    {
        return $amount > 0 ? numberFormat($amount) : '0.00';
    }
}