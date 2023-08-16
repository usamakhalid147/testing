<?php

/**
 * Reservation Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    ReservationModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CurrencyConversion;

class RoomReservation extends Model
{
    use HasFactory,CurrencyConversion;

    /**
     * The attributes are converted based on session currency.
     *
     * @var array
     */
    public $convert_fields = ['base_price','total_price','promotion_amount'];

    protected $casts = [
        'applied_promotions' => 'array'
    ];

    /**
     * Join With Hotel Sub Room
     *
     */
    public function hotel_room()
    {
        return $this->belongsTo('App\Models\HotelRoom','room_id','id');
    }

    public function getTotalWithDiscountAttribute()
    {
        return numberFormat($this->total_price + $this->promotion_amount);
    }

    public function getPromotionAmountAttribute()
    {
        $applied_promotions = $this->applied_promotions ?? [];
        $total = 0;
        foreach($applied_promotions as $promotion) {
            $total += $promotion['amount'];
        }

        return $total;
    }
}
