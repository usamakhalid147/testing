<?php

/**
 * LoginSlider Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    LoginSliderModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginSlider extends Model
{
    use HasFactory;

	/**
	 * Where the Files are stored
	 *
	 * @var string
	 */
	public $filePath = "/images/sliders";

	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
	public $appends = ['image_src'];

	/**
     * Scope to Order Records Based on order_id
     *
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_id');
    }
}
