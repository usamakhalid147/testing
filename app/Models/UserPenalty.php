<?php

/**
 * User Penalty Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    UserPenaltyModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CurrencyConversion;

class UserPenalty extends Model
{
	use HasFactory,CurrencyConversion;

	/**
	 * The attributes are converted to session currency.
	 *
	 * @var array
	 */
	public $convert_fields = ['total','paid','remaining'];

	/**
	 * Join With User Table
	 *
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
