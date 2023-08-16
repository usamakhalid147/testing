<?php

/**
 * Amenity Type Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    AmenityTypeModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class AmenityType extends Model
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

	/**
     * Join With Amenity Table
     *
     */
    public function amenities()
    {
        return $this->hasMany(Amenity::class);
    }
}
