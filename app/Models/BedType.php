<?php

/**
 * Bed Type Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    BedTypeModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class BedType extends Model
{
    use HasFactory, HasTranslations;

	/**
	 * The attributes that are Translatable
	 *
	 * @var array
	 */
	public $translatable = ['name'];

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
	public $filePath = "/images/bed_types";
}
