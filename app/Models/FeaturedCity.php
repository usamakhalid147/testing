<?php

/**
 * Featured City Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    FeaturedCityModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeaturedCity extends Model
{
	/**
	 * Where the Files are stored
	 *
	 * @var string
	 */
	public $filePath = "/images/featured_cities";

	/**
	 * Get the Search Url
	 *
	 * @return String SearchUrl
	 */
	public function getSearchUrlAttribute()
    {
        return resolveRoute('search',['place_id'=> $this->place_id,'location'=> $this->city_name]);
    }

    /**
     * Scope to Order Records Based on order_id
     *
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_id');
    }
}
