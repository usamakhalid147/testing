<?php

/**
 * User Information Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    UserInformationModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserInformation extends Model
{
    use HasFactory;
    
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_informations';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['dob_formatted'];

	/**
     * Get Date of Birth Attribute as Carbon Object
     *
     */
    public function getDobAttribute()
    {
        if($this->attributes['dob'] == '') {
            return \Carbon\Carbon::createFromFormat('Y-m-d',"0001-00-00");
        }
        return \Carbon\Carbon::createFromFormat('Y-m-d',$this->attributes['dob']);
    }

    /**
     * Get Date of Birth Attribute as Carbon Object
     *
     */
    public function getDateOfBirthAttribute()
    {
        if($this->attributes['dob'] == '') {
            return '-';
        }
        return $this->dob->format(DATE_FORMAT);
    }

    /**
     * Get Date of Birth Attribute as Carbon Object
     *
     */
    public function getDobFormattedAttribute()
    {
        return $this->dob->format('F d, Y');
    }

    /**
     * Get User Address
     * 
     */
    public function getAddressAttribute()
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
     * Get User Preferred language
     *
     */
    public function getLanguageArrayAttribute()
    {
        $languages = explode(',', $this->attributes['languages']);
        $language_list = resolve("Language")->where('status',1)->where('is_translatable',1)->whereIn('code',$languages);
        $return_data['code'] = $language_list->pluck('code')->toArray();
        $return_data['name'] = $language_list->pluck('name')->toArray();
        $return_data['display_list'] = implode(', ',$return_data['name']);
        return $return_data;
    }
  
}
