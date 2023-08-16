<?php

/**
 * PopularCity Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    PopularCityModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PopularCity extends Model
{
    use HasFactory;

    /**
     * Join Popular Localities table
     * 
     */
    public function popular_localities()
    {
        return $this->hasMany('App\Models\PopularLocality','popular_city_id','id');
    }
}
