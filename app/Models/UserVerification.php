<?php

/**
 * User Verification Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    UserVerificationModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserVerification extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
    public $timestamps = false;

    protected $verification_list = ['email','phone_number','facebook','google','apple'];

    /**
     * Get the User Total verification count
     *
     * @return String ImageUrl
     */
    public function getVerifiedCountAttribute()
    {
    	$verfied_count = 0;
    	foreach ($this->verification_list as $list) {
			if($this->attributes[$list] == 1) {
				$verfied_count++;
			}
		}
		return $verfied_count;
	}

	/**
	 * Check User complete atleast one verification
	 *
	 * @return Boolean
	 */
	public function canShow()
	{
		foreach ($this->verification_list as $list) {
			if($this->attributes[$list] == 1) {
				return true;
			}
		}
		return false;
    }
}
