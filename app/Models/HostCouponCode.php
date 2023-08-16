<?php

/**
 * Host Coupon Code Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    HostCouponCodeModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CurrencyConversion;

class HostCouponCode extends Model
{
    use HasFactory,CurrencyConversion;


    /**
     * The attributes are converted to session currency.
     *
     * @var array
     */
    public $convert_fields = ['amount','min_amount'];

    public function getAmountAttribute()
    {
        return $this->attributes['value'];
    }

    public function getCouponValueAttribute()
    {
        if($this->type == 'amount') {
            return $this->currency_symbol.$this->amount;
        }
        return $this->value.'%';
    }

    /**
     * Scope to return Valid Records Only
     *
     * @param Qeury Builder $query
     * @return Qeury Builder
     */
    public function scopeValid($query)
    {
        return $query->whereDate('start_date','<=',date('Y-m-d'))->whereDate('end_date','>=',date('Y-m-d'));
    }

    /**
     * Scope to get the details based on current user
     *
     */
    public function scopeAuthUser($query)
    {   
        $user_id = getCurrentUserId();
        if(isHost()) {
            $user_id = getHostId();
        }
        return $query->where('user_id', $user_id);
    }

    /**
     * Scope to return publicly visible Coupons only
     *
     * @param Query Builder $query
     * @return Query Builder
     */
    public function scopePubliclyVisible($query)
    {
        return $query->where('visible_on_public',1);
    }

    /**
     * Scope to return Active and Valid Records Only
     *
     * @param Qeury Builder $query
     * @return Qeury Builder
     */
    public function scopeActive($query)
    {
        return $query->activeOnly()->valid();
    }

    /**
     * Get display_text Attribute
     *
     * @return String
     */
    public function getDisplayTextAttribute()
    {
        return \Lang::get('messages.apply_coupon_to_get',['amount' => $this->currency_symbol.$this->amount,'code' => $this->code]);
    }
}
