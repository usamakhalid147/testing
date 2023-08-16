<?php

/**
 * Payout Method Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    PayoutMethodModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayoutMethod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','payout_method'];

	/**
     * Get Auth User Based Records
     *
     */
    public function scopeAuthBased($query)
    {
        $user_id = getCurrentUserId();
        if(isHost()) {
            $user_id = getHostId();
        }
        return $query->where('user_id', $user_id);
    }

    /**
     * Join With Payout Method Details Table
     *
     */
    public function payout_method_detail()
    {
        return $this->hasOne(PayoutMethodDetail::class);
    }
}
