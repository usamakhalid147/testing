<?php

/**
 * Slider Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    SliderModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class Slider extends Model
{
    use HasFactory, HasTranslations;

	/**
	 * The attributes that are Translatable
	 *
	 * @var array
	 */
	public $translatable = ['title', 'description'];

	/**
	 * Where the Files are stored
	 *
	 * @var string
	 */
	public $filePath = "/images/sliders";

	/**
	 * Where the Files are stored
	 *
	 * @var string
	 */
	public $imageSize = ['width' => 1500,'height' => 1000];

	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
	public $appends = ['image_src'];

	/**
	 * Scope to return Active Records Only
	 *
	 * @param Query Builder $query
	 * @param String $type
	 * @return Query Builder
	 */
	public function scopeTypeBased($query,$type)
	{
		return $query->where('type',$type);
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
