<?php

/**
 * PopularLocality Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    PopularLocalityModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PopularLocality extends Model
{
    use HasFactory;

    /**
     * Join Popular City table
     * 
     */
    public function popular_city()
    {
        return $this->hasOne('App\Models\PopularCity','id','popular_city_id');
    } 
}
