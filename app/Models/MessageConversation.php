<?php

/**
 * Message Conversation Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    MessageConversationModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lang;

class MessageConversation extends Model
{
    use HasFactory;

    /**
     * Remove Email or Phone number from message
     *
     */
    public function setMessageAttribute($value)
    {
        $this->attributes['message'] = removeEmailNumber($value);
    }

    /**
     * Join With Message Sent User Record
     *
     */
    public function from_user()
    {
        return $this->belongsTo(User::class,'user_from');
    }

    /**
     * Join With Message Received User Record
     *
     */
    public function to_user()
    {
        return $this->belongsTo(User::class,'user_to');
    }

    /**
     * Get Message Sent At time in Y-m-d format
     *
     */
    public function getSentAtAttribute()
    {
        if(\Auth::check()) {
            $timezone = \Auth::user()->timezone;
        }

        $created_at = $this->created_at;
        if(isset($timezone)) {
            $created_at = $created_at->setTimeZone($timezone);
        }

        $sent_at = '';
        if(date('Y-m-d') != $created_at->format('Y-m-d')) {
            $sent_at = $created_at->format(DATE_FORMAT).' ';
        }
        $sent_at .= $created_at->format(TIME_FORMAT);

        return $sent_at;
    }

    /**
     * Get Message Header Notification Text
     *
     */
    public function getHeaderNotificationTextAttribute()
    {
        $user_id = getCurrentUserId();
        if($this->message_type == '2') {
            return Lang::get('messages.booking_confirmed');
        }

        if($this->message_type == '3') {
            if($this->user_from == $user_id) {
                return Lang::get('messages.request_sent');
            }
            return Lang::get('messages.request_received');
        }

        if($this->message_type == '4') {
            if($this->user_from == $user_id) {
                return Lang::get('messages.you_pre_accepted_booking');
            }
            return Lang::get('messages.booking_pre_accepted');
        }

        if($this->message_type == '5') {
            return Lang::get('messages.request_declined');
        }

        if($this->message_type == '6') {
            return Lang::get('messages.request_expired');
        }

        if($this->message_type == '7') {
            return Lang::get('messages.request_cancelled');
        }

        if($this->message_type == '9') {
            return Lang::get('messages.booking_cancelled');
        }

        if($this->message_type == '14') {
            $special_offer = $this->special_offer;
            if($this->user_from == $user_id) {
                return Lang::get('messages.you_sent_special_offer_to_guest',['replace_key_1' => $special_offer->currency_symbol.''.$special_offer->day_price]);
            }
            return Lang::get('messages.your_host_sent_special_offer_to_you',['replace_key_1' => $this->from_user->first_name,'replace_key_2' => $special_offer->currency_symbol.''.$special_offer->day_price]);
        }
        return '';
    }
}
