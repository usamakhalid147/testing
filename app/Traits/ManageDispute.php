<?php

/**
 * Trait for Manage Dispute
 *
 * @package     HyraHotel
 * @subpackage  Traits
 * @category    ManageDispute
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Traits;

use App\Models\Dispute;
use App\Models\DisputeDocument;
use Lang;

trait ManageDispute
{
    /**
     * Join With Dispute Table
     *
     */
    public function dispute()
    {
        return $this->hasOne(\App\Models\Dispute::class);
    }

    /**
     * Get Max Days for Apply Dispute
     *
     */
    public function getMaxDisputeDays()
    {
        $max_day = getCurrentUserId() == $this->user_id ? MAX_GUEST_DISPUTE_DAYS : MAX_HOST_DISPUTE_DAYS;
        return $max_day;
    }

    /**
     * Get Host Maximum Dispute Amount
     *
     */
    public function maxHostDisputeAmount()
    {
        return $this->security_fee;
    }

    /**
     * Get Guest Maximum Dispute Amount
     *
     */
    public function maxGuestDisputeAmount()
    {
        return $this->calcHostPayoutAmount();
    }

    /**
     * Get Maximum Dispute Amount
     *
     */
    public function maxDisputeAmount()
    {
        if(getCurrentUserId() == $this->host_id) {
            return $this->maxHostDisputeAmount();
        }
        return $this->maxGuestDisputeAmount();
    }

    /**
     * Get Last Date For Apply Dispute
     *
     */
    public function getLastDisputeDate()
    {
        $max_days = $this->getMaxDisputeDays();
        if($this->status == 'Cancelled') {
            $end_date = getDateObject($this->cancelled_at);
        }
        else {
            $end_date = getDateObject($this->checkout);
        }

        $end_date->addDays($max_days);
        return $end_date;
    }

    /**
     * Get Remaining Days for Apply Dispute
     *
     */
    public function getDisputeDays()
    {
        $today = getDateObject();
        $end_date = $this->getLastDisputeDate();

        $interval_days = $today->diff($end_date)->format('%R%a');
        return $interval_days + 1;
    }

    /**
     * check has Processing Dispute
     *
     */
    public function hasProcessingDispute()
    {
        if($this->canApplyToDispute()) {
            return true;
        }
        if($this->dispute != '') {
            if($this->dispute->status == 'closed' && $this->dispute->admin_status == 'closed') {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Check user can able to apply for dispute
     *
     */
    public function canApplyToDispute()
    {
        if(!in_array($this->status,['Accepted','Cancelled'])) {
            return false;
        }

        if($this->status == 'Accepted' && !$this->checkoutCrossed()) {
            return false;
        }

        if($this->status == 'Cancelled' && $this->cancelled_at < getDateObject($this->checkin)) {
            return false;
        }

        $dispute_days = $this->getDisputeDays();
        if($dispute_days <= 0 || $this->getMaxDisputeDays() < $dispute_days) {
            return false;
        }

        if($this->dispute != '') {
            return false;
        }

        $max_amount = $this->maxDisputeAmount();
        if($max_amount == 0) {
            return false;
        }

        return true;
    }
}