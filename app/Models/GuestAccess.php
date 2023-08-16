<?php

/**
 * Guest Access Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    GuestAccessModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class GuestAccess extends Model
{
    use HasFactory, HasTranslations;

	/**
	 * The attributes that are Translatable
	 *
	 * @var array
	 */
	public $translatable = ['name', 'description'];

    /**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;
}
