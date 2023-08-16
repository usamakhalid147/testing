<?php

/**
 * Global Setting Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    GlobalSettingModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class GlobalSetting extends Model
{
    use HasFactory;
    
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * Where the Files are stored
	 *
	 * @var string
	 */
	public $filePath = "/images/logos";
}
