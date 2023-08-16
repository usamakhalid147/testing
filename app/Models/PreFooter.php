<?php

/**
 * PreFooter Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    PreFooterModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class PreFooter extends Model
{
    use HasFactory, HasTranslations;

	/**
	 * The attributes that are Translatable
	 *
	 * @var array
	 */
	public $translatable = ['title', 'description'];

	/**
	 * Where the Files are stored
	 *
	 * @var string
	 */
	// public $filePath = "/images/pre_footers";
}
