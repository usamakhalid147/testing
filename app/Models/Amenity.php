<?php

/**
 * Amenity Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    AmenityModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class Amenity extends Model
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
	 * Where the Files are stored
	 *
	 * @var string
	 */
	public $filePath = "/images/amenities";

    /**
	* Join With Amenity Type location Table
	*
	*/
	public function amenity_type()
	{
		return $this->belongsTo(AmenityType::class);
	}

	/**
     * Get Amenity Type Name
     *
     * @return String
     */
    public function getAmenityTypeNameAttribute()
    {
    	return $this->amenity_type->name;
    }
}
