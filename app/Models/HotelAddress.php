<?php

/**
 * Hotel Address Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    HotelAddressModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelAddress extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'hotel_id';

    /**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

    /**
     * Get Formatted Address line to display
     *
     */
    public function getAddressLineDisplayAttribute()
    {
        $address_line = ' ';
        if($this->city) {
            $address_line .= $this->city.', ';
        }
        if($this->state) {
            $address_line .= $this->state.', ';
        }
        if($this->country_code) {
            $address_line .= $this->country_code;
        }
        return rtrim($address_line,', ');
    }

    /**
     * Get Full Adderss
     *
     */
    public function getFullAddressAttribute()
    {
        $address_line = $this->address_line_1.', ';
        if($this->address_line_2 != '') {
            $address_line .= $this->address_line_2.', ';
        }
        if($this->city != '') {
            $address_line .= $this->city.', ';
        }
        if($this->state != '') {
            $address_line .= $this->state.', ';
        }
        if($this->country_name != '') {
            $address_line .= $this->country_name;
        }

        $address_line = rtrim($address_line,', ');

        if($this->postal_code != '') {
            $address_line .= ' - '.$this->postal_code;
        }
        return $address_line;
    }

    /**
    * Get The Name of the country by coutry code
    *
    */
    public function getCountryNameAttribute()
    {
        $country_list = resolve('Country');
        $country = $country_list->where('name',$this->country_code)->first();
        return optional($country)->full_name;
    }
}
