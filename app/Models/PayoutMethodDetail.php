<?php

/**
 * Payout Method Details Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    PayoutMethodDetailModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayoutMethodDetail extends Model
{
	use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['payout_method_id'];
}
