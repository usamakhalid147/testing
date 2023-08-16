<?php

/**
 * Hotel Room Price Model
 *
 * @package     HyraHotels
 * @subpackage  Models
 * @category    HotelRoomPriceModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CurrencyConversion;

class HotelRoomPrice extends Model
{
    use HasFactory,CurrencyConversion;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['hotel_id','room_id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * The attributes are converted based on session currency.
     *
     * @var array
     */
    public $convert_fields = ['price','extra_price'];
    
    /**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

    public function hotel_room()
    {
        return $this->belongsTo('App\Models\HotelRoom','room_id');
    }

    public function getCurrencySymbolAttribute()
    {
        $currency = Currency::where('code',$this->currency_code)->first();
        return $currency->symbol;
    }

}
