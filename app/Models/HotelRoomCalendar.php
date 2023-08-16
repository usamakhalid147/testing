<?php

/**
 * Hotel Room Calendar Model
 *
 * @package     HyraHotels
 * @subpackage  Models
 * @category    HotelRoomCalendarModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CurrencyConversion;
use Lang;

class HotelRoomCalendar extends Model
{
    use HasFactory,CurrencyConversion;

    /**
     * The attributes are converted based on session currency.
     *
     * @var array
     */
    public $convert_fields = ['price'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Join With Room Table
     * 
     */
    public function hotel_room() 
    {
        return $this->belongsTo(HotelRoom::class,'room_id','id');
    }

    /**
     * Get Display Color Attibute
     *
     */
    public function getDisplayColorAttribute()
    {
        return $this->status == 'not_available' ? '#de472f' : '#ff6b6ba1';
    }

    /**
     * Get Title Attibute
     *
     */
    public function getTitleAttribute()
    {
        if($this->notes != '') {
            return $this->notes;
        }
        return \Lang::get('messages.'.$this->status);
    }

    public function scopeDaysNotAvailable($query, $days = array())
    {
        return $query->whereIn('reserve_date', $days)->where('status','not_available');
    }
}
