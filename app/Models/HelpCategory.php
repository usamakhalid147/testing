<?php

/**
 * Help Category Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    HelpCategoryModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;

class HelpCategory extends Model
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
	public $filePath = "/images/help_categories";

	/**
     * Join With helps Table
     *
     */
    public function helps()
    {
        return $this->hasMany(Help::class,'category_id')->where('status',1);;
    }

    /**
     * Join With help Category Table with Parent Category
     *
     */
    public function help_category()
    {
        return $this->belongsTo(HelpCategory::class,'category_id');
    }

    /**
     * Join With help Category Table with Parent Category
     *
     */
    public function categories()
    {
        return $this->hasMany(HelpCategory::class,'category_id');
    }

    /**
     * Join With help Category Table with Parent Category
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
        return $this->help_category()->with('help_category');
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
