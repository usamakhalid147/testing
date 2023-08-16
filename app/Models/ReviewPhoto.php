<?php

/**
 * Review Photo Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    ReviewPhotoModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewPhoto extends Model
{
    use HasFactory;

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
    protected $appends = ['image_src'];

    /**
     * Where the Files are stored
     *
     * @var string
     */
    public $filePath = "/images/reviews";

    /**
     * Scope to Order Records Based on order_id
     *
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_id');
    }

    /**
     * Get Image Upload Path
     *
     * @return String filePath
     */
    public function getUploadPath()
    {
        return $this->filePath.'/'.$this->review_id;
    }
}