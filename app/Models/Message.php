<?php

/**
 * Message Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    MessageModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['list_type','reservation_id'];

    /**
     * Remove Email or Phone number from message
     *
     */
    public function setGuestMessageAttribute($value)
    {
        $this->attributes['guest_message'] = removeEmailNumber($value);
    }

    /**
     * Remove Email or Phone number from message
     *
     */
    public function setHostMessageAttribute($value)
    {
        $this->attributes['host_message'] = removeEmailNumber($value);
    }

    /**
     * Scope to Get Records Based on current login user
     *
     * @return Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeAuthUser($query)
    {
        $user_id = getCurrentUserId();
        return $query->where(function($query) use($user_id) {
            $query->where('user_id',$user_id)->orwhere('host_id',$user_id);
        });
    }

    /**
     * Scope to Get Records Based on current login user and user Type
     *
     * @return Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeUserBased($query,$type = 'Guest')
    {
        $user_id = getCurrentUserId();
        $column = ($type == 'Guest') ? 'user_id' : 'host_id';
        return $query->where($column,$user_id);
    }

    /**
     * Scope to Get Type Based Records
     *
     */
    public function scopeListTypeBased($query,$type)
    {
        return $query->where('list_type',$type);
    }

    /**
     * Scope to Get Not archived Records
     *
     */
    public function scopeNotArchived($query,$user_type)
    {
        return $query->where($user_type.'_archive','0');
    }

    /**
     * Scope to Get Unread Records
     *
     */
    public function scopeUnreadOnly($query)
    {
        return $query;
    }

    /**
     * Join With User Table by Guest
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Join With User Table by Host
     *
     */
    public function host_user()
    {
        return $this->belongsTo(User::class, 'host_id', 'id');
    }

    /**
     * Join With Reservation Table
     *
     */
    public function hotel_reservation()
    {
        return $this->belongsTo(Reservation::class,'reservation_id');
    }

    /**
     * Join With Hotel Table
     *
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class,'list_id');
    }

    /**
     * Join With Experience Table
     *
     */
    /*ExperienceCommentStart*/
    public function experience()
    {
        return $this->belongsTo(\Modules\Experience\Models\Experience::class,'list_id');
    }
    /*ExperienceCommentEnd*/

    /**
     * Join With Message Conversation Table
     *
     */
    public function conversations()
    {
        return $this->hasMany(MessageConversation::class);
    }

    /**
     * Get the Listing based on the type
     *
     */
    public function reservation()
    {
        $reservation = $this->list_type.'_reservation';
        return $this->$reservation;
    }

    /**
     * Check Reservation has Valid Special Offer
     *
     */
    public function hasValidSpecialOffer()
    {
        if($this->special_offer_id <= 0) {
            return false;
        }

        $special_offer = $this->special_offer;
        if($special_offer->status != 'pending' || $special_offer->status == 'removed') {
            return false;
        }

        if($special_offer->isExpired()) {
            return false;
        }

        return true;
    }

    /**
     * Get the List type Text Attribute
     *
     */
    public function getListTypeTextAttribute()
    {
        return Lang::get('messages.'.$payout->list_type);
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

        $updated_at = $this->updated_at;
        if(isset($timezone)) {
            $updated_at = $updated_at->setTimeZone($timezone);
        }

        $sent_at = '';
        if(date('Y-m-d') != $updated_at->format('Y-m-d')) {
            $sent_at = $updated_at->format(DATE_FORMAT);
        }
        else {
            $sent_at = $updated_at->format(TIME_FORMAT);
        }

        return $sent_at;
    }
}
