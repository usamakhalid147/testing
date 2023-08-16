<?php

/**
 * Help Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    HelpModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class Help extends Model
{
    use HasFactory, HasTranslations;

	/**
	 * The attributes that are Translatable
	 *
	 * @var array
	 */
	public $translatable = ['title', 'content'];

	/**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
	 * Join With help category Table
	 *
	 */
	public function help_category()
	{
		return $this->belongsTo(HelpCategory::class,'category_id');
	}

	/**
	 * Get Help Category name
	 *
	 */
	public function getCategoryNameAttribute()
	{
		return $this->help_category->title;
	}

	/**
	 * Get Published Time Attribute
	 *
	 */
	public function getPublishedAtAttribute()
	{
		return $this->created_at->format('F d, Y');
	}

	/**
	 * Get Short Answer Attribute
	 *
	 */
	public function getShortAnswerAttribute()
	{
		$answer = \Str::of(strip_tags($this->content))->substr(0,200).'...';
		return html_entity_decode($answer);
	}
}
