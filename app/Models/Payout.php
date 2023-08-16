<?php

/**
 * Payout Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    PayoutModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CurrencyConversion;

class Payout extends Model
{
    use HasFactory,CurrencyConversion;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','reservation_id','user_type','list_type'];

    /**
     * The attributes are converted to session currency.
     *
     * @var array
     */
    public $convert_fields = ['amount','penalty'];

    /**
     * Scope to Get Type Based Records
     *
     */
    public function scopeListTypeBased($query,$type)
    {
        return $query->where('list_type',$type);
    }

    /**
     * Scope to get the details based on current user
     *
     */
    public function scopeAuthUser($query)
    {
        return $query->where('user_id', getCurrentUserId());
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
     * Join With Hotel Table
     *
     */
	public function hotel()
	{
		return $this->belongsTo(Hotel::class,'list_id');
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
     * Get the Listing based on the type
     *
     */
    public function reservation()
    {
        $reservation = $this->list_type.'_reservation';
        return $this->$reservation;
    }

    public function getListTypeTextAttribute()
    {
        return Lang::get('messages.'.$payout->list_type);
    }
}
