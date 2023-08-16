<?php

/**
 * Blog Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    BlogModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class Blog extends Model
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
	 * Join With blog category Table
	 *
	 */
	public function blog_category()
	{
		return $this->belongsTo(BlogCategory::class,'category_id');
	}

	/**
	 * Get Blog Category name
	 *
	 */
	public function getCategoryNameAttribute()
	{
		return $this->blog_category->title;
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
