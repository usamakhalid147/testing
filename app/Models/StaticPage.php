<?php

/**
 * Static Page Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    StaticPageModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class StaticPage extends Model
{
    use HasFactory, HasTranslations;

    /**
	 * The attributes that are Translatable
	 *
	 * @var array
	 */
	public $translatable = ['name', 'content'];

	/**
	 * Get Url of the Static Page
	 *
	 */
	public function getUrlAttribute()
	{
		return resolveRoute("static_page",['slug' => $this->slug]);
	}
}
