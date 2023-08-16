<?php

/**
 * Company Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    CompanyModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class Company extends Model
{
    use HasFactory;

    /**
     * Where the Files are stored
     *
     * @var string
     */
    public $filePath = "/images/company";

    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = ['logo_src'];

        /**
     * Get Image Upload Path
     *
     * @return String filePath
     */
    public function getUploadPath()
    {
        return $this->filePath.'/'.$this->user_id;
    }

    /**
     * Get the Image Url
     *
     * @return String ImageUrl
     */
    public function getLogoSrcAttribute()
    {
        if(!$this->id || $this->company_logo == '') {
            return asset('images/preview_thumbnail.png');
        }

        $handler = $this->getImageHandler();
        $image_data['name'] = $this->company_logo;
        $image_data['path'] = $this->getUploadPath();
        $image_data['image_size'] = $this->getImageSize();
        $image_data['version_based'] = false;

        return $handler->fetch($image_data);
    }
    public function deleteImageFile()
    {
        $handler = $this->getImageHandler();
        $image_data['name'] = $this->company_logo;
        $image_data['target_dir'] = $this->getUploadPath();
        return $handler->destroy($image_data);
    }

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
