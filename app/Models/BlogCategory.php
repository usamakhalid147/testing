<?php

/**
 * Blog Category Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    BlogCategoryModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class BlogCategory extends Model
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
	public $filePath = "/images/blog_categories";

	/**
     * Join With blogs Table
     *
     */
    public function blogs()
    {
        return $this->hasMany(Blog::class,'category_id')->where('status',1);;
    }

    /**
     * Join With blog Category Table with Parent Category
     *
     */
    public function blog_category()
    {
        return $this->belongsTo(BlogCategory::class,'category_id');
    }

    /**
     * Join With blog Category Table with Parent Category
     *
     */
    public function categories()
    {
        return $this->hasMany(BlogCategory::class,'category_id');
    }

    /**
     * Join With blog Category Table with Parent Category
     *
     */
    public function child_categories()
    {
        return $this->categories()->with('categories');
    }

    /**
     * Get All Child Categories
     *
     */
    public function parent_category()
    {
        return $this->blog_category()->with('blog_category');
    }

    /**
     * Check Current Category Has Parent Category Or Not
     *
     * @return Boolean
     */
    public function getHasParentAttribute()
    {
        return ($this->category_id !== 0);
    }
}
