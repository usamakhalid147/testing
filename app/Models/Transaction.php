<?php

/**
 * Transaction Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    TransactionModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CurrencyConversion;

class Transaction extends Model
{
	use HasFactory, CurrencyConversion;

	/**
     * The attributes are converted based on session currency.
     *
     * @var array
     */
    public $convert_fields = ['amount'];

    /**
     * Scope to Get List Type Based Records
     *
     */
    public function scopeListTypeBased($query,$type)
    {
        return $query->where('list_type',$type);
    }

    /**
     * Scope to Get Type Based Records
     *
     */
    public function scopeTypeBased($query,$type)
    {
        return $query->where('type',$type);
    }

    /**
     * Scope to Get Incoming Based Records
     *
     */
    public function scopeIncomeOnly($query)
    {
        return $query->whereIn('type',['booking','dispute']);
    }

    /**
     * Scope to Get Outgoing Based Records
     *
     */
    public function scopeOutgoingOnly($query)
    {
        return $query->whereIn('type',['payout','refund']);
    }

    /**
	 * Join With Guest User Table
	 *
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
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

    /**
	 * Get link Attibute
	 *
	 */
	public function getLinkAttribute()
	{
		return route('admin.reservations.show',['id' => $this->reservation_id]);
	}

    /**
     * Get Transaction Type Attibute
     *
     */
    public function getTransactionTypeAttribute()
    {
        if(in_array($this->type,['reservation','dispute'])) {
            return 'incoming';
        }

        if(in_array($this->type,['payout','refund'])) {
            return 'outgoing';
        }

        return '';
    }

    /**
     * Get Amount Color Attibute
     *
     */
    public function getColorAttribute()
    {
        if(in_array($this->type,['reservation','dispute'])) {
            return 'success';
        }

        if(in_array($this->type,['payout','refund'])) {
            return 'danger';
        }

        return 'default';
    }
}
