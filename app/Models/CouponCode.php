<?php

/**
 * Coupon Code Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    CouponCodeModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CurrencyConversion;

class CouponCode extends Model
{
    use HasFactory,CurrencyConversion;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * The attributes are converted to session currency.
     *
     * @var array
     */
    public $convert_fields = ['amount'];

    /**
     * Scope to return Valid Records Only
     *
     * @param Query Builder $query
     * @return Query Builder
     */
    public function scopeValid($query)
    {
        return $query->whereDate('start_date','<=',date('Y-m-d'))->whereDate('end_date','>=',date('Y-m-d'));
    }

    /**
     * Scope to Get Type Based Records
     *
     * @param Query Builder $query
     * @return Query Builder
     */
    public function scopeListTypeBased($query,$type)
    {
        return $query->where(function($query) {
            $query->where('list_type',$type)->orWhere('list_type',NULL);
        });
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
     * @param Query Builder $query
     * @return Query Builder
     */
    public function scopeActive($query)
    {
        return $query->activeOnly()->valid();
    }

    /**
     * Get Amount Attribute
     *
     * @return String
     */
    public function getAmountAttribute()
    {
        return $this->attributes['value'];
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
