<?php

/**
 * Referral User Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    ReferralUserModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CurrencyConversion;

class ReferralUser extends Model
{
    use HasFactory, CurrencyConversion;

    /**
     * The attributes are converted based on session currency.
     *
     * @var array
     */
    public $convert_fields = ['user_credited_amount','referral_credited_amount','user_become_guest_amount'];

    /**
     * Scope to Get Records Based on current login user
     *
     * @return Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeAuthUser($query)
    {
        $user_id = getCurrentUserId();
        return $query->where(function($query) use($user_id) {
            $query->where('user_id',$user_id)->orwhere('referral_user_id',$user_id);
        });
    }

    /**
     * Join With User Table
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Join With User Table
     *
     */
    public function referral_user()
    {
        return $this->belongsTo(User::class,'referral_user_id');
    }

    /**
     * Get Maximum Earnable amount by this referral
     *
     */
    public function getMaxCreditableAmountAttribute()
    {
        return numberFormat($this->user_become_guest_amount);
    }
}
